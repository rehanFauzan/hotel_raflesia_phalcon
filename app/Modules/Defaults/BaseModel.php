<?php

namespace App\Modules\Defaults;

use Phalcon\Mvc\Model as PhalconModel;

class BaseModel extends PhalconModel
{
    /**
     * Stores original primary key values after fetch, used to detect PK changes on update.
     * @var array<string, mixed>
     */
    protected $_originalPrimaryKeyValues = [];

    public function onConstruct()
    {
        $this->useDynamicUpdate(true);
        $this->keepSnapshots(true);
    }

    public function afterFetch()
    {
        $metaData = $this->getModelsMetaData();
        $primaryKeys = $metaData->getPrimaryKeyAttributes($this);
        $original = [];
        foreach ($primaryKeys as $primaryKeyColumn) {
            $original[$primaryKeyColumn] = $this->readAttribute($primaryKeyColumn);
        }
        $this->_originalPrimaryKeyValues = $original;
    }

    /**
     * Override update to allow changing primary key values using model->save()/update().
     */
    public function update($data = null, $whiteList = null): bool
    {
        if (is_array($data)) {
            $this->assign($data, $whiteList);
        }

        $metaData = $this->getModelsMetaData();
        $primaryKeys = $metaData->getPrimaryKeyAttributes($this);

        if (empty($primaryKeys)) {
            return parent::update($data, $whiteList);
        }

        $hasPrimaryKeyChanged = false;
        $oldPrimaryKeyValues = [];
        foreach ($primaryKeys as $primaryKeyColumn) {
            $currentValue = $this->readAttribute($primaryKeyColumn);
            $oldValue = $this->_originalPrimaryKeyValues[$primaryKeyColumn] ?? $currentValue;
            $oldPrimaryKeyValues[$primaryKeyColumn] = $oldValue;
            if ($currentValue !== $oldValue) {
                $hasPrimaryKeyChanged = true;
            }
        }

        if ($hasPrimaryKeyChanged === false) {
            return parent::update($data, $whiteList);
        }

        $changedFields = $this->getChangedFields();
        $setColumns = [];
        $bind = [];

        if (empty($changedFields) && is_array($data)) {
            $changedFields = array_keys($data);
        }

        foreach ($primaryKeys as $pk) {
            if (!in_array($pk, $changedFields, true)) {
                $changedFields[] = $pk;
            }
        }

        $changedFields = array_values(array_unique($changedFields));
        foreach ($changedFields as $columnName) {
            $value = $this->readAttribute($columnName);
            $setColumns[] = '[' . $columnName . '] = ' . $this->toSqlLiteral($value);
        }

        if (empty($setColumns)) {
            return true;
        }

        $whereParts = [];
        foreach ($primaryKeys as $primaryKeyColumn) {
            $whereParts[] = '[' . $primaryKeyColumn . '] = ' . $this->toSqlLiteral($oldPrimaryKeyValues[$primaryKeyColumn]);
        }

        $schema = $this->getSchema();
        $table = $this->getSource();
        $qualifiedTable = $schema ? ('[' . $schema . '].[' . $table . ']') : ('[' . $table . ']');

        $sql = 'UPDATE ' . $qualifiedTable . ' SET ' . implode(', ', $setColumns) . ' WHERE ' . implode(' AND ', $whereParts);

        $connection = $this->getWriteConnection();
        $connection->execute($sql);

        foreach ($primaryKeys as $primaryKeyColumn) {
            $this->_originalPrimaryKeyValues[$primaryKeyColumn] = $this->readAttribute($primaryKeyColumn);
        }

        $this->refresh();
        return true;
    }

    /**
     * Convert PHP value to a safe inline SQL literal without using bindings.
     */
    protected function toSqlLiteral($value): string
    {
        if ($value === null) {
            return 'NULL';
        }
        if ($value instanceof \DateTimeInterface) {
            $value = $value->format('Y-m-d H:i:s');
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        // Always use Unicode literal for safety on SQL Server
        $string = (string) $value;
        $escaped = str_replace("'", "''", $string);
        return "N'" . $escaped . "'";
    }

    public function assignWithSkip($data, $skipOnCreate = [], $skipOnUpdate = [])
    {
        if (!empty($skipOnCreate)) {
            $this->skipAttributesOnCreate($skipOnCreate);
        }

        if (!empty($skipOnUpdate)) {
            $this->skipAttributesOnUpdate($skipOnUpdate);
        }

        $this->assign($data);
        return $this;
    }
}

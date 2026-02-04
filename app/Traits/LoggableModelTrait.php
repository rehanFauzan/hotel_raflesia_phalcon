<?php

namespace App\Traits;

use App\Libraries\Log;

trait LoggableModelTrait
{
    protected $oldData = [];
    protected $oldDataDelete = [];

    // public function beforeUpdate()
    // {
    //     $this->oldData = $this->getSnapshotData();
    // }

    public function beforeCreate()
    {
        $now = date('Y-m-d H:i:s');
        if (property_exists($this, 'created_at')) {
            $this->created_at = $now;
        }
        if (property_exists($this, 'updated_at')) {
            $this->updated_at = $now;
        }
    }

    public function beforeUpdate()
    {
        $primaryKey = $this->getPrimaryKey();
        $old = static::findFirst([$primaryKey . " = :id:", 'bind' => ['id' => $this->$primaryKey]]);
        $this->oldData = $old ? $old->toArray() : [];

        // Set updated_at jika ada field-nya
        if (property_exists($this, 'updated_at')) {
            $this->updated_at = date('Y-m-d H:i:s');
        }
    }

    public function beforeDelete()
    {
        $primaryKey = $this->getPrimaryKey();
        $old = static::findFirst([$primaryKey . " = :id:", 'bind' => ['id' => $this->$primaryKey]]);
        $this->oldData = $old ? $old->toArray() : [];
    }

    protected function getPrimaryKey()
    {
        // Ambil field primary dari metadata (otomatis)
        $meta = $this->getModelsMetaData();
        $primaryKeys = $meta->getPrimaryKeyAttributes($this);
        return $primaryKeys[0] ?? 'id'; // fallback ke 'id'
    }

    public function afterUpdate()
    {
        Log::write(
            'Melakukan perubahan data pada modul atau class ' . static::class,
            $this->oldData,
            $this->toArray(),
            TRUE,
            $this->getControllerName(),
            'update'
        );
    }

    public function afterCreate()
    {
        Log::write(
            'Melakukan penambahan data pada modul atau class ' . static::class,
            null,
            $this->toArray(),
            TRUE,
            $this->getControllerName(),
            'create'
        );
    }

    public function afterDelete()
    {
        Log::write(
            'Melakukan penghapusan data pada modul atau class ' . static::class,
            $this->oldData,
            $this->toArray(),
            TRUE,
            $this->getControllerName(),
            'delete'
        );
    }

    protected function getControllerName()
    {
        // Optional: Ambil controller aktif dari router, fallback ke class name
        $di = \Phalcon\Di::getDefault();
        if ($di->has('router')) {
            $controller = $di->get('router')->getControllerName();
            return $controller ?: static::class;
        }
        return static::class;
    }
}

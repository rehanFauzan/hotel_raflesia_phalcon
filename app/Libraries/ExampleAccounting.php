<?php


use App\Libraries\DatabaseSP\DatabaseSP;
use App\Libraries\DatabaseSP\Driver;
use App\Libraries\DatabaseSP\FetchMode;
use App\Libraries\DatabaseSp\DB;
use App\Libraries\DatabaseSP\DatabaseSPException;

/**
 * Example 7: Practical Real-World Usage (Based on Your Original Code)
 */
class ExampleAccounting
{
    private DatabaseSP $db;

    public function __construct(DatabaseSP $db)
    {
        $this->db = $db;
    }

    /**
     * MySQL: Get closing data (CALL sp_closing)
     */
    public function getClosingData(int $pdamId, int $ofId): array
    {
        return $this->db
            ->call('sp_closing', [
                'pdam_id' => $pdamId,
                'of_id'   => $ofId,
            ])
            ->fetchAll();
    }

    /**
     * SQL Server: Upsert journal entry
     */
    public function upsertJournalEntry(array $journalData): ?array
    {
        return $this->db
            ->call('transjurnal_dhhd_upsert_mjournal_v2', [
                'vid'         => $journalData['id'],
                'vmjo_date'   => $journalData['date'],
                'vmjo_desc'   => $journalData['description'],
                'vmjo_amount' => $journalData['amount'],
                'vmjo_type'   => $journalData['type'],
                'vcreated_by' => $journalData['created_by'],
            ], 'akuntansi')
            ->fetchOne();
    }

    /**
     * Batch journal entries within transaction
     */
    public function batchJournalEntries(array $entries): bool
    {
        return $this->db->transaction(function (DatabaseSP $db) use ($entries) {
            foreach ($entries as $entry) {
                $db->call('transjurnal_dhhd_upsert_mjournal_v2', [
                    'vid'         => $entry['id'],
                    'vmjo_date'   => $entry['date'],
                    'vmjo_desc'   => $entry['description'],
                    'vmjo_amount' => $entry['amount'],
                ], 'akuntansi');
            }
            return true;
        });
    }
}

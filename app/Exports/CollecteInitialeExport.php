<?php

namespace App\Exports;

use App\Models\CollecteBienInitiale;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CollecteInitialeExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private readonly ?string $emplacement = null,
        private readonly ?string $lotUid = null
    ) {
    }

    public function collection(): Collection
    {
        $query = CollecteBienInitiale::query()->orderByDesc('id');

        if ($this->emplacement) {
            $query->where('emplacement_label', 'like', '%' . $this->emplacement . '%');
        }

        if ($this->lotUid) {
            $query->where('lot_uid', $this->lotUid);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Lot UID',
            'Ligne',
            'Emplacement',
            'Affectation',
            'Designation',
            'Quantite',
            'Etat',
            'Observations',
            'Transcription',
            'Confiance',
            'Agent',
            'Date creation',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->lot_uid,
            $row->line_index,
            $row->emplacement_label,
            $row->affectation_label,
            $row->designation,
            $row->quantite,
            $row->etat,
            $row->observations,
            $row->transcription_brute,
            $row->confiance,
            $row->agent_label,
            optional($row->created_at)?->format('Y-m-d H:i:s'),
        ];
    }
}

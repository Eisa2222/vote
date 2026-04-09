<?php

namespace App\Imports;

use App\Models\Import;
use App\Models\Player;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class PlayersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    private int $clubId;
    private Import $import;
    private int $rowCount = 0;
    private int $successCount = 0;
    private int $failedCount = 0;
    private array $errors = [];

    public function __construct(int $clubId, Import $import)
    {
        $this->clubId = $clubId;
        $this->import = $import;
    }

    public function model(array $row)
    {
        $this->rowCount++;

        try {
            $player = Player::create([
                'club_id' => $this->clubId,
                'name' => $row['name'] ?? $row['الاسم'] ?? '',
                'national_id' => $row['national_id'] ?? $row['رقم_الهوية'] ?? null,
                'player_number' => $row['player_number'] ?? $row['رقم_اللاعب'] ?? null,
                'phone' => $row['phone'] ?? $row['الجوال'] ?? null,
                'email' => $row['email'] ?? $row['البريد'] ?? null,
                'nationality' => $row['nationality'] ?? $row['الجنسية'] ?? null,
                'position' => $row['position'] ?? $row['المركز'] ?? null,
                'status' => 'active',
            ]);

            $this->successCount++;
            return $player;
        } catch (\Exception $e) {
            $this->failedCount++;
            $this->errors[] = "صف {$this->rowCount}: " . $e->getMessage();
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required_without:الاسم',
            'الاسم' => 'required_without:name',
        ];
    }

    public function getRowCount(): int { return $this->rowCount; }
    public function getSuccessCount(): int { return $this->successCount; }
    public function getFailedCount(): int { return $this->failedCount; }
    public function getErrors(): array { return $this->errors; }
}

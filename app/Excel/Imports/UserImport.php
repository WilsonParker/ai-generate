<?php

namespace App\Excel\Imports;

use App\Models\User\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class UserImport implements ToCollection
{
    public function __construct() {}

    public function collection(Collection $collection)
    {
        foreach ($collection as $idx => $row) {
            if ($idx < $this->getHeaderIndex()) {
                continue;
            }
            if (!$this->hasRow($row)) {
                break;
            }
            dump($idx);

            $user = User::create([
                'name' => $this->getName($row),
                'email' => $this->getEmail($row),
            ]);

            $user->information()->create([
                'google_id' => '',
                'avatar' => $this->getAvatar($row),
                'locale' => $this->getLocale($row),
                'introduction' => $this->getDescription($row),
            ]);
            $user->save();
        }
    }

    private function getHeaderIndex(): int
    {
        return 2;
    }

    private function hasRow(Collection $row): bool
    {
        return !empty($row[2]);
    }

    private function getName(Collection $row): string
    {
        return $row[1];
    }

    private function getEmail(Collection $row): string
    {
        return $row[2];
    }

    private function getAvatar(Collection $row): string
    {
        return $row[5];
    }

    private function getLocale(Collection $row): string
    {
        return $row[3];
    }

    private function getDescription(Collection $row): string
    {
        return $row[4];
    }

    private function getId(Collection $row): string
    {
        return $row[0];
    }
}

<?php

namespace Database\Seeders;

use App\Models\BreedDefault;
use Illuminate\Database\Seeder;

class BreedDefaultSeeder extends Seeder
{
    public function run(): void
    {
        $breeds = [
            'canine' => [
                'Labrador Retriever', 'Golden Retriever', 'Poodle', 'Bulldog Francês',
                'Pastor Alemão', 'Rottweiler', 'Beagle', 'Shih Tzu', 'Yorkshire Terrier',
                'Dachshund', 'Boxer', 'Pug', 'Husky Siberiano', 'Border Collie',
                'Maltês', 'Lhasa Apso', 'Chihuahua', 'Cocker Spaniel Inglês',
                'Buldogue Inglês', 'Dobermann', 'Akita', 'Shiba',
                'Vira-Lata (SRD)',
            ],
            'feline' => [
                'Abissínio', 'American', 'American Bobtail', 'American Curl',
                'Aphrodite', 'Australian Mist', 'Balinese', 'Bengal', 'Birman',
                'Bombay', 'British', 'Burmese', 'Burmilla', 'Chartreux', 'Chausie',
                'Cornish Rex', 'Cymric', 'Devon Rex', 'Donskoy', 'Egyptian Mau',
                'Exotic Shorthair', 'Havana', 'Highlander', 'Himalayan',
                'Japanese Bobtail', 'Khaomanee', 'Korat', 'Kurilian Bobtail',
                'Laperm', 'Lykoi', 'Maine Coon', 'Manx', 'Minuet', 'Munchkin',
                'Nebelung', 'Norueguês da Floresta', 'Ocicat', 'Oriental',
                'Persa', 'Peterbald', 'Pixiebob', 'Ragdoll', 'Russian Blue',
                'Savannah', 'Scottish Fold', 'Scottish Straight', 'Selkirk Rex',
                'Serengeti', 'Siamês', 'Siberian', 'Singapura', 'Snowshoe',
                'Somali', 'Sphynx', 'Thai', 'Tonkinese', 'Toybob', 'Toyger',
                'Turkish Angora', 'Turkish Van', 'Vira-Lata (SRD)',
            ],
        ];

        foreach ($breeds as $species => $names) {
            foreach ($names as $name) {
                BreedDefault::firstOrCreate(
                    ['species' => $species, 'breed' => $name],
                    ['is_active' => true]
                );
            }
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\{Category, Department, Instruction, Part, Brand, GovernmentResource, User};
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Роли и отделы
        foreach (['admin', 'it_support', 'manager', 'employee'] as $r) Role::firstOrCreate(['name' => $r]);
        $deptIt = Department::firstOrCreate(['name' => 'ИТ-отдел']);
        $deptAcc = Department::firstOrCreate(['name' => 'Бухгалтерия']);
        $deptHr = Department::firstOrCreate(['name' => 'Отдел кадров']);

        // 2. Пользователи
        $users = [
            ['admin', 'Иванов Иван Иванович', 'admin1@mats.ru', $deptIt],
            ['admin', 'Петров Петр Петрович', 'admin2@mats.ru', $deptIt],
            ['it_support', 'Сидоров Сергей Сергеевич', 'tech1@mats.ru', $deptIt],
            ['it_support', 'Кузнецова Анна Павловна', 'tech2@mats.ru', $deptIt],
            ['it_support', 'Волков Владимир Игоревич', 'tech3@mats.ru', $deptIt],
            ['it_support', 'Соловьева Ольга Игоревна', 'tech4@mats.ru', $deptIt],
            ['manager', 'Лебедева Татьяна Сергеевна', 'manager1@mats.ru', $deptAcc],
            ['employee', 'Попов Дмитрий Алексеевич', 'employee1@mats.ru', $deptHr],
            ['employee', 'Васильева Ольга Николаевна', 'employee2@mats.ru', $deptHr],
        ];

        foreach ($users as $u) {
            $user = User::firstOrCreate(['email' => $u[2]], [
                'name' => $u[1],
                'password' => bcrypt('password'),
                'department_id' => $u[3]->id,
                'position' => 'Специалист',
            ]);
            $user->assignRole($u[0]);
        }

        // 3. Данные для модулей
        $brands = ['HP', 'Lenovo', 'Cisco', 'Xerox', 'Samsung', 'Dell'];
        foreach ($brands as $b) Brand::firstOrCreate(['name' => $b], ['slug' => strtolower($b)]);

        $techs = User::role('it_support')->get();
        $categories = [
            'Не включается компьютер' => ['Блок питания ATX 500W', 'Кабель питания 220V'],
            'Не печатает принтер' => ['Картридж HP 107A', 'Ролик захвата бумаги'],
            'Нет доступа к сети' => ['Патч-корд RJ-45 2м', 'Сетевая карта PCI-E'],
            'Проблемы с монитором' => ['Кабель HDMI-HDMI', 'Матрица 21.5"'],
        ];

        foreach ($categories as $catName => $parts) {
            $cat = Category::firstOrCreate(['name' => $catName], ['default_assignee_id' => $techs->first()->id]);
            // Привязываем случайных техников к категории
            $cat->technicians()->sync($techs->random(2)->pluck('id'));
            
            Instruction::firstOrCreate(['title' => "Инструкция: $catName", 'category_id' => $cat->id], [
                'steps' => ['Проверить подключение', 'Перезагрузить устройство', 'Проверить настройки', 'Создать обращение']
            ]);

            foreach ($parts as $pName) {
                Part::firstOrCreate(['name' => $pName, 'category_id' => $cat->id], [
                    'sku' => strtoupper(substr(str_replace(' ', '', $pName), 0, 5)) . '-' . rand(100, 999),
                    'brand_id' => Brand::inRandomOrder()->first()->id,
                    'quantity' => rand(5, 30)
                ]);
            }
        }

        GovernmentResource::firstOrCreate(['name' => 'Госуслуги', 'url' => 'https://www.gosuslugi.ru']);
        GovernmentResource::firstOrCreate(['name' => 'Минцифры РФ', 'url' => 'https://digital.gov.ru']);
    }
}
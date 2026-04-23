<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Department;
use App\Models\GovernmentResource;
use App\Models\Instruction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles
        $roleEmployee = Role::create(['name' => 'employee']);
        $roleSupport = Role::create(['name' => 'it_support']);
        $roleManager = Role::create(['name' => 'manager']);
        $roleAdmin = Role::create(['name' => 'admin']);

        // 2. Department
        $deptIt = Department::create(['name' => 'Отдел информационного обеспечения']);
        $deptGeneral = Department::create(['name' => 'Общий отдел']);

        // 3. Admin
        $admin = User::create([
            'name' => 'Администратор Системы',
            'email' => 'admin@mats.ru',
            'password' => bcrypt('password'),
            'department_id' => $deptIt->id,
            'position' => 'Главный администратор',
            'phone' => '100',
            'cabinet' => '100',
        ]);
        $admin->assignRole($roleAdmin);

        // 4. Technician (Support)
        $tech = User::create([
            'name' => 'Технический специалист',
            'email' => 'tech@mats.ru',
            'password' => bcrypt('password'),
            'department_id' => $deptIt->id,
            'position' => 'Старший техник',
            'phone' => '101',
            'cabinet' => '101',
        ]);
        $tech->assignRole($roleSupport);

        // 5. Manager
        $manager = User::create([
            'name' => 'Менеджер портала',
            'email' => 'manager@mats.ru',
            'password' => bcrypt('password'),
            'department_id' => $deptGeneral->id,
            'position' => 'Контент-менеджер',
            'phone' => '102',
            'cabinet' => '102',
        ]);
        $manager->assignRole($roleManager);

        // 6. Employee
        $user = User::create([
            'name' => 'Иванов Иван',
            'email' => 'ivanov@mats.ru',
            'password' => bcrypt('password'),
            'department_id' => $deptGeneral->id,
            'position' => 'Специалист',
            'phone' => '202',
            'cabinet' => '202',
        ]);
        $user->assignRole($roleEmployee);

        // 7. Categories
        $catCartridge = Category::create([
            'name' => 'Замена картриджа',
            'default_assignee_id' => $tech->id,
        ]);

        $catNetwork = Category::create([
            'name' => 'Не работает сеть',
            'default_assignee_id' => $tech->id,
        ]);

        // 8. Instructions (Steps)
        Instruction::create([
            'category_id' => $catCartridge->id,
            'title' => 'Как заменить картридж самостоятельно',
            'steps' => [
                'Откройте переднюю крышку принтера',
                'Извлеките старый картридж, потянув его на себя',
                'Распакуйте новый картридж и аккуратно встряхните его 5-6 раз',
                'Установите новый картридж в направляющие до щелчка',
                'Закройте крышку и дождитесь прогрева принтера'
            ],
        ]);

        Instruction::create([
            'category_id' => $catNetwork->id,
            'title' => 'Первичная диагностика сети',
            'steps' => [
                'Проверьте физическое подключение кабеля к компьютеру',
                'Убедитесь, что индикатор на сетевой карте мигает зеленым',
                'Попробуйте перезагрузить компьютер',
                'Проверьте, работают ли сетевые ресурсы у коллег'
            ],
        ]);

        // 9. Government Resources
        GovernmentResource::create(['name' => 'Госуслуги', 'url' => 'https://www.gosuslugi.ru']);
        GovernmentResource::create(['name' => 'Минсельхоз РФ', 'url' => 'https://mcx.gov.ru']);
        GovernmentResource::create(['name' => 'Администрация Волгоградской области', 'url' => 'https://www.volgograd.ru']);
    }
}

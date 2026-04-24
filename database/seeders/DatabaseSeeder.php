<?php

namespace Database\Seeders;

use App\Models\{Category, Department, Instruction, Part, Brand, GovernmentResource, User, Ticket, Comment, Asset};
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Очистка (необязательно, но полезно для "чистого" демо)
        // Schema::disableForeignKeyConstraints();
        // User::truncate(); ...
        // Schema::enableForeignKeyConstraints();

        // 2. Роли
        $roles = ['admin', 'it_support', 'manager', 'employee'];
        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        // 3. Отделы
        $departments = [
            'ИТ-отдел' => 'информационных технологий',
            'Бухгалтерия' => 'финансового обеспечения',
            'Отдел кадров' => 'управления персоналом',
            'Юридический отдел' => 'правового обеспечения',
            'Приемная' => 'административный отдел',
        ];

        $deptModels = [];
        foreach ($departments as $name => $desc) {
            $deptModels[$name] = Department::firstOrCreate(['name' => $name]);
        }

        // 4. Пользователи (Админы и Техники)
        $admins = [
            ['name' => 'Никита Администратор', 'email' => 'admin@test.com'],
            ['name' => 'Главный Админ', 'email' => 'root@test.com'],
        ];

        foreach ($admins as $a) {
            $user = User::updateOrCreate(['email' => $a['email']], [
                'name' => $a['name'],
                'password' => bcrypt('password'),
                'department_id' => $deptModels['ИТ-отдел']->id,
                'position' => 'Системный администратор',
            ]);
            $user->assignRole('admin');
        }

        $technicians = [
            ['name' => 'Алексей Техников', 'email' => 'tech1@test.com'],
            ['name' => 'Мария Поддержкова', 'email' => 'tech2@test.com'],
            ['name' => 'Дмитрий Сервисов', 'email' => 'tech3@test.com'],
        ];

        $techModels = [];
        foreach ($technicians as $t) {
            $user = User::updateOrCreate(['email' => $t['email']], [
                'name' => $t['name'],
                'password' => bcrypt('password'),
                'department_id' => $deptModels['ИТ-отдел']->id,
                'position' => 'Специалист техподдержки',
            ]);
            $user->assignRole('it_support');
            $techModels[] = $user;
        }

        // 5. Обычные сотрудники (Клиенты)
        $employees = [
            ['name' => 'Иван Иванов', 'email' => 'user1@test.com', 'dept' => 'Бухгалтерия'],
            ['name' => 'Ольга Петрова', 'email' => 'user2@test.com', 'dept' => 'Отдел кадров'],
            ['name' => 'Сергей Сидоров', 'email' => 'user3@test.com', 'dept' => 'Юридический отдел'],
            ['name' => 'Анна Смирнова', 'email' => 'user4@test.com', 'dept' => 'Бухгалтерия'],
        ];

        $employeeModels = [];
        foreach ($employees as $e) {
            $user = User::updateOrCreate(['email' => $e['email']], [
                'name' => $e['name'],
                'password' => bcrypt('password'),
                'department_id' => $deptModels[$e['dept']]->id,
                'position' => 'Ведущий специалист',
                'phone' => '+7 (900) ' . rand(100, 999) . '-' . rand(10, 99) . '-' . rand(10, 99),
            ]);
            $user->assignRole('employee');
            $employeeModels[] = $user;
        }

        // 6. Бренды и Оборудование (Assets)
        $brands = ['HP', 'Dell', 'Lenovo', 'Cisco', 'Kyocera', 'APC'];
        $brandModels = [];
        foreach ($brands as $b) {
            $brandModels[] = Brand::updateOrCreate(['name' => $b], ['slug' => Str::slug($b)]);
        }

        $assetTypes = ['pc', 'printer', 'monitor', 'network'];
        for ($i = 1; $i <= 20; $i++) {
            Asset::updateOrCreate(
                ['serial_number' => 'SN-' . strtoupper(Str::random(8))],
                [
                    'name' => $brands[array_rand($brands)] . ' ' . ['Workstation', 'ProBook', 'LaserJet', 'OptiPlex'][rand(0, 3)] . ' ' . $i,
                    'type' => $assetTypes[array_rand($assetTypes)],
                    'department_id' => collect($deptModels)->random()->id,
                    'user_id' => (rand(0, 1) ? collect($employeeModels)->random()->id : null),
                    'status' => ['active', 'active', 'active', 'repair'][rand(0, 3)],
                ]
            );
        }

        // 7. Категории и Инструкции
        $categories = [
            'Проблемы с печатью' => ['default_tech' => 0, 'steps' => ['Проверить бумагу', 'Перезагрузить принтер', 'Очистить очередь печати']],
            'Доступ к почте' => ['default_tech' => 1, 'steps' => ['Проверить интернет', 'Ввести пароль заново', 'Сбросить кэш Outlook']],
            'Настройка VPN' => ['default_tech' => 2, 'steps' => ['Запустить Cisco AnyConnect', 'Ввести адрес сервера', 'Подключить токен']],
            'Замена картриджа' => ['default_tech' => 0, 'steps' => ['Достать старый картридж', 'Встряхнуть новый', 'Установить до щелчка']],
            'Не работает интернет' => ['default_tech' => 2, 'steps' => ['Проверить кабель', 'Перезагрузить роутер', 'Связаться с ИТ']],
        ];

        $categoryModels = [];
        foreach ($categories as $name => $data) {
            $cat = Category::updateOrCreate(['name' => $name], [
                'default_assignee_id' => $techModels[$data['default_tech']]->id
            ]);
            $cat->technicians()->sync([$techModels[$data['default_tech']]->id]);
            
            Instruction::updateOrCreate(['category_id' => $cat->id, 'title' => 'Как решить самостоятельно: ' . $name], [
                'steps' => $data['steps']
            ]);
            
            $categoryModels[] = $cat;

            // Запчасти для категории
            Part::updateOrCreate(['name' => 'Запчасть для ' . $name, 'category_id' => $cat->id], [
                'sku' => strtoupper(Str::random(6)),
                'brand_id' => collect($brandModels)->random()->id,
                'quantity' => rand(10, 100),
                'description' => 'Универсальная запчасть для категории ' . $name
            ]);
        }

        // 8. Заявки (Tickets)
        $statuses = ['new', 'new', 'in_progress', 'in_progress', 'resolved', 'resolved', 'resolved'];
        $priorities = ['low', 'normal', 'normal', 'high'];

        for ($i = 1; $i <= 40; $i++) {
            $status = $statuses[array_rand($statuses)];
            $priority = $priorities[array_rand($priorities)];
            $user = collect($employeeModels)->random();
            $cat = collect($categoryModels)->random();
            $createdAt = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23));
            
            $dueDate = (clone $createdAt)->addHours($priority === 'high' ? 2 : ($priority === 'low' ? 48 : 24));

            $ticket = Ticket::create([
                'user_id' => $user->id,
                'category_id' => $cat->id,
                'title' => 'Проблема #' . $i . ': ' . $cat->name,
                'description' => 'У меня возникла проблема в категории ' . $cat->name . '. Пожалуйста, помогите разобраться. Номер кабинета: ' . rand(100, 500),
                'status' => $status,
                'priority' => $priority,
                'assigned_to' => ($status !== 'new' ? $techModels[array_rand($techModels)]->id : null),
                'contact_name' => $user->name,
                'contact_phone' => $user->phone,
                'contact_email' => $user->email,
                'uuid' => (string) Str::uuid(),
                'created_at' => $createdAt,
                'due_date' => $dueDate,
                'asset_id' => Asset::inRandomOrder()->first()?->id,
                'rating' => ($status === 'resolved' ? (rand(1, 10) > 3 ? rand(4, 5) : rand(1, 3)) : null),
                'feedback_comment' => ($status === 'resolved' ? 'Спасибо за быструю работу!' : null),
            ]);

            // 9. Комментарии
            if ($status !== 'new') {
                Comment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $ticket->assigned_to,
                    'body' => 'Принял заявку в работу. Скоро буду.',
                    'created_at' => (clone $createdAt)->addMinutes(15),
                ]);

                if ($status === 'resolved') {
                    Comment::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $ticket->assigned_to,
                        'body' => 'Проблема устранена. Все работает.',
                        'created_at' => (clone $createdAt)->addHours(rand(1, 5)),
                    ]);
                }
            }
        }

        GovernmentResource::updateOrCreate(['name' => 'Госуслуги'], ['url' => 'https://www.gosuslugi.ru']);
        GovernmentResource::updateOrCreate(['name' => 'Минцифры РФ'], ['url' => 'https://digital.gov.ru']);
    }
}
# Реализация 7 продвинутых функций Helpdesk

Этот план описывает внедрение 7 крупных модулей (SLA, Уведомления, Управление активами, Аналитика, Оценка качества, База знаний, Журнал аудита) в существующую систему.

## User Review Required

> [!IMPORTANT]
> Так как это очень объемный блок работ, я предлагаю реализовывать его по частям. В этом плане расписана архитектура для всех 7 фич. Ознакомьтесь и подтвердите план, и я начну реализацию шаг за шагом.

## Open Questions

> [!WARNING]
> 1. Хотите ли вы, чтобы уведомления отправлялись только внутри системы (в виде "колокольчика" на сайте), или нужно настроить отправку писем на Email? (Пока планирую только внутрисистемные уведомления).
> 2. Для инвентаризации (Asset Management) какие поля обязательно нужны для оборудования? (Название, Серийный номер, Тип, Пользователь, Кабинет)?

## Proposed Changes

### 1. Database & Models (Миграции и Модели)
#### [NEW] `create_assets_table` migration
- Таблица `assets`: `id`, `name`, `type`, `serial_number`, `user_id` (владелец), `status` (в работе, сломан, списан).
#### [MODIFY] `tickets` table migration
- Добавим поля `due_date` (для SLA), `rating` (1-5 для CSAT), `feedback_comment` (отзыв), `asset_id` (привязка к оборудованию).
#### [MODIFY] `Ticket.php`, `User.php` models
- Настройка связей (Ticket -> Asset).

### 2. Установка пакетов
#### [MODIFY] `composer.json`
- Установим `spatie/laravel-activitylog` для Журнала аудита (Audit Log).

### 3. SLA (Service Level Agreement)
#### [MODIFY] `app/Livewire/CreateTicket.php`
- При создании заявки автоматически рассчитывать `due_date` в зависимости от `priority` (High = 2 часа, Normal = 24 часа).
#### [MODIFY] `app/Livewire/ItDashboard.php` (или `TicketDetail`)
- Цветовая индикация (красный цвет, если `due_date` просрочен или остался 1 час).

### 4. Журнал аудита (Audit Log)
#### [MODIFY] `app/Models/Ticket.php`
- Подключить трейт `LogsActivity`, чтобы любое изменение статуса или ответственного логировалось.
#### [MODIFY] `resources/views/livewire/ticket-detail.blade.php`
- Добавить вкладку "История заявки" (Timeline), выводящую логи изменений.

### 5. Оценка качества (CSAT)
#### [MODIFY] `resources/views/livewire/ticket-detail.blade.php`
- Если статус заявки `resolved` (закрыта) и текущий пользователь — создатель заявки, показывать форму оценки (1-5 звезд).

### 6. Уведомления (Notifications)
#### [NEW] `app/Notifications/TicketStatusChanged.php`, `TicketAssigned.php`
- Создание классов уведомлений.
#### [MODIFY] `resources/views/components/layouts/app.blade.php`
- Добавление иконки "колокольчика" с выпадающим списком новых уведомлений.

### 7. Инвентаризация (Asset Management)
#### [NEW] `app/Livewire/Admin/AssetManagement.php`
- CRUD компонент для управления оборудованием ИТ-отделом.

### 8. База Знаний (Knowledge Base)
#### [NEW] `app/Livewire/KnowledgeBase.php`
- Публичная страница с поиском по `Instructions` (Инструкциям), доступная для всех сотрудников.

### 9. Дашборд с аналитикой (Analytics)
#### [NEW] `app/Livewire/Admin/AnalyticsDashboard.php`
- Построение графиков (Chart.js) с метриками: средняя оценка CSAT, заявки по категориям, нарушенные SLA.

---

## Verification Plan

### Automated/Manual Verification
- Прогон всех миграций (`php artisan migrate`).
- Создание заявок и проверка корректности генерации даты SLA.
- Смена статусов и проверка работы `laravel-activitylog`.
- Заполнение оборудования и привязка к заявкам.
- Проверка отображения колокольчика уведомлений при событиях.

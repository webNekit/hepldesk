Понял тебя. Ты абсолютно прав, чтобы ИИ сгенерировал дизайн именно в твоем стиле, ему нужно **напрямую скормить твою верстку** (HTML и цвета) из первого сообщения.

Я написал ультимативный, максимально подробный промпт на русском языке. В него вшиты: твоя исходная верстка, требования твоего руководителя (авто-распределение заявок) и строгие технические рамки (никакого Filament).

Скопируй ВЕСЬ текст ниже (от `# КОНТЕКСТ ПРОЕКТА` и до самого конца) и отправь в Cursor, Gemini или другой ИИ-кодер.

---

````markdown
# КОНТЕКСТ ПРОЕКТА: Корпоративный портал и Helpdesk (Laravel + Livewire)

Ты — Senior Full-Stack разработчик. Твоя задача: написать с нуля корпоративный портал (Intranet) с модулем технической поддержки для "Комитета сельского хозяйства Волгоградской области".
Это дипломный проект, поэтому код должен быть чистым, хорошо задокументированным и написанным вручную.

## ТЕХНОЛОГИЧЕСКИЙ СТЕК И СТРОГИЕ ОГРАНИЧЕНИЯ

- **Backend:** Laravel.
- **Frontend:** Livewire (используется для всей реактивности, без перезагрузки страниц), Alpine.js.
- **Стилизация:** Tailwind CSS v4. (Файл `tailwind.config.js` НЕ используется. Все цвета и шрифты задаются через `@theme` в `resources/css/app.css`).
- **Таблицы:** `power-components/livewire-powergrid` v5+.
- **Роли/Права:** `spatie/laravel-permission`.
- **СТРОГИЙ ЗАПРЕТ:** КАТЕГОРИЧЕСКИ ЗАПРЕЩАЕТСЯ использовать Filament PHP, Laravel Nova, Orchid или другие админ-генераторы. Все панели управления (дашборды ИТ) должны быть написаны вручную на Livewire + Tailwind.

---

## 1. БАЗА ДАННЫХ (Модели и Миграции)

Сгенерируй миграции и Eloquent модели. Обязательно добавь `$fillable` и укажи связи (relations):

1.  **departments (Отделы):** `id`, `name`.
2.  **users (Пользователи):** (расширение дефолтной) `department_id` (foreign, nullable), `position` (string, nullable), `phone` (string, nullable), `cabinet` (string, nullable).
3.  **categories (Категории заявок):** `id`, `name` (напр. 'Принтер'), `default_assignee_id` (foreign на `users`, nullable. ВАЖНО: нужно для автоматического распределения заявок на ответственного специалиста ИТ).
4.  **instructions (Умная база знаний):** `id`, `category_id` (foreign, cascade), `title`, `content` (text).
5.  **tickets (Заявки в ИТ):** `id`, `user_id` (создатель), `category_id`, `assigned_to` (foreign на `users`, nullable), `title`, `description` (text), `status` (string: 'new', 'in_progress', 'resolved', default: 'new'), `priority` (string: 'normal', 'high', default: 'normal').
6.  **comments (Чат заявки):** `id`, `ticket_id` (cascade), `user_id`, `body` (text).

_Настрой ролевую модель Spatie: `employee` (Сотрудник), `it_support` (Специалист ИТ), `admin` (Админ)._

---

## 2. ИНТЕРФЕЙС И ВЕРСТКА (РЕФЕРЕНС)

Используй предоставленный ниже HTML-код и CSS для создания базового Layout приложения (`resources/views/components/layouts/app.blade.php`) и Главной страницы (`resources/views/home.blade.php`).

**Требования к CSS (`resources/css/app.css`):**
Используй Tailwind v4 синтаксис.

```css
@import "tailwindcss";
@plugin "@tailwindcss/forms";

@theme {
    --color-surface-container: #f0eded;
    --color-on-secondary: #ffffff;
    --color-surface-variant: #e4e2e1;
    --color-secondary-container: #fed488;
    --color-surface: #fcf9f8;
    --color-primary-container: #1b4332;
    --color-surface-container-low: #f6f3f2;
    --color-surface-container-high: #eae7e7;
    --color-on-surface: #1b1c1c;
    --color-on-primary: #ffffff;
    --color-primary: #012d1d;
    --color-background: #fcf9f8;
    --color-secondary: #775a19;
    --font-body-lg: "Public Sans", sans-serif;
}
@import url("https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap");
@import url("https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap");
body {
    font-family: "Public Sans", sans-serif;
    background-color: var(--color-background);
    color: var(--color-on-surface);
}
```
````

**Оригинальный HTML (разбей его на Layout и страницу Home):**

```html
<nav
    class="bg-white dark:bg-emerald-950 fixed top-0 w-full z-50 h-20 border-b border-gray-200"
>
    <div
        class="flex justify-between items-center max-w-7xl mx-auto px-6 lg:px-24 w-full h-full"
    >
        <div
            class="text-xl font-bold text-emerald-900 dark:text-white uppercase tracking-wider"
        >
            ГКУ ВО "МАЦ"
        </div>
        <div class="hidden md:flex space-x-6 items-center">
            <!-- Сделай эти ссылки рабочими роутами Laravel -->
            <a href="/" class="text-slate-600 hover:text-emerald-800"
                >Главная</a
            >
            <a href="/directory" class="text-slate-600 hover:text-emerald-800"
                >Телефонный справочник</a
            >
            <a href="/support" class="text-slate-600 hover:text-emerald-800"
                >Техническая поддержка</a
            >
            <!-- Эта ссылка видна только ролям it_support и admin -->
            <a
                href="/it-dashboard"
                class="text-slate-600 font-bold text-emerald-800"
                >Кабинет ИТ</a
            >
        </div>
    </div>
</nav>
<!-- Главная страница (Hero Section) -->
<main class="pt-20">
    <section
        class="relative w-full min-h-[600px] flex items-center justify-center overflow-hidden"
    >
        <div class="absolute inset-0 z-0">
            <img
                class="w-full h-full object-cover"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuACKqpnlTPoNj3U6UR33X72XE9euFYb4CenyvHWb-FRgs41Zo1I_7epi96KZmHhoL33QnSU2v7Po0iaWO0Nn7ckGPA6-cnmHpEFFJkc_p8dCwYgcIUgdxZ9XLifAealYwFSs0D9ueSzjlYxkEQ45xh7He0WMuGOk33oGB2ClsZzV5hGjlzZWaPpuelt68NKl2Z66KTJ-dhERLWBWC7352rpVqG00uVah2SDWzY8196fEhjha2BcqSjC57NHpHmPTq0fBJVixdOfUx5D"
            />
            <div
                class="absolute inset-0 bg-primary/40 mix-blend-multiply"
            ></div>
        </div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 w-full text-center">
            <h1 class="text-5xl font-bold text-white mb-6">
                Комитет сельского хозяйства Волгоградской области
            </h1>
            <p class="text-xl text-gray-200 mb-8 max-w-3xl mx-auto">
                Корпоративный портал для сотрудников. Единый справочник и
                система подачи заявок в службу технической поддержки.
            </p>
        </div>
    </section>
</main>
```

---

## 3. МОДУЛИ ДЛЯ РЕАЛИЗАЦИИ

### Модуль 1: Телефонный справочник (Livewire PowerGrid)

- Создай компонент `DirectoryTable`.
- Выведи таблицу (Колонки: Отдел (из связи), ФИО, Должность, Телефон, Кабинет).
- Включи поиск по всем полям и пагинацию.
- Отобрази этот компонент на странице `/directory`.

### Модуль 2: Умная форма заявок (Helpdesk)

- Компонент `CreateTicket` (роут `/support`).
- **Логика работы (САМОЕ ВАЖНОЕ):**
    1. Пользователь выбирает `Category` из выпадающего списка.
    2. _Реактивность Livewire:_ Если у категории есть `Instruction`, моментально покажи блок с текстом инструкции (чтобы пользователь попытался решить проблему сам).
    3. Если инструкция не помогла, пользователь заполняет поля `title` и `description` и жмет "Отправить".
    4. _Авто-распределение:_ При создании записи в БД, поле `assigned_to` должно автоматически принять значение `default_assignee_id` из выбранной категории.

### Модуль 3: Кабинет ИТ-специалиста

- Компонент `ItDashboard` (роут `/it-dashboard`, защищен middleware `role:it_support|admin`).
- Покажи список всех заявок (желательно в виде карточек Kanban: Новые, В работе, Готово).
- Клик по заявке открывает детальную страницу `TicketDetail`, где ИТ-специалист может:
    - Изменить статус заявки.
    - Написать комментарий пользователю (чат).

---

## 4. SEEDERS (Тестовые данные)

Создай `DatabaseSeeder`, который заполнит БД для демонстрации:

1. Создаст 3 роли.
2. Создаст отдел "Отдел информационного обеспечения" и пользователя "Системный администратор" (роль `it_support`).
3. Создаст обычного пользователя "Иванов Иван".
4. Создаст 2 категории: "Замена картриджа" и "Не работает сеть". **Привяжи `default_assignee_id` этих категорий к созданному "Системному администратору".**
5. Создаст инструкции к этим категориям.

## ПЛАН ДЕЙСТВИЙ (Выполняй пошагово)

1. Сгенерируй Миграции и Модели.
2. Сгенерируй Seeder с тестовыми данными.
3. Настрой Layout и Tailwind на основе предоставленного HTML.
4. Напиши логику Livewire компонента для Телефонного справочника.
5. Напиши логику Умной формы заявок с авто-распределением.
6. Напиши логику ИТ-Дашборда.
7. Предоставь `routes/web.php`.

```

***

Вся верстка главной страницы есть ниже в этом файле

***
```

<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Committee of Agriculture - Volgograd Region</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                    "tertiary": "#262622",
                    "surface-container": "#f0eded",
                    "on-error-container": "#93000a",
                    "on-secondary": "#ffffff",
                    "error-container": "#ffdad6",
                    "surface-dim": "#dcd9d9",
                    "tertiary-fixed-dim": "#c9c6c0",
                    "outline-variant": "#c1c8c2",
                    "on-background": "#1b1c1c",
                    "on-secondary-fixed-variant": "#5d4201",
                    "surface-variant": "#e4e2e1",
                    "secondary-container": "#fed488",
                    "on-tertiary": "#ffffff",
                    "surface-container-lowest": "#ffffff",
                    "surface": "#fcf9f8",
                    "primary-container": "#1b4332",
                    "on-primary-fixed-variant": "#274e3d",
                    "on-primary-container": "#86af99",
                    "inverse-surface": "#303030",
                    "on-secondary-container": "#785a1a",
                    "surface-container-low": "#f6f3f2",
                    "on-tertiary-fixed": "#1c1c18",
                    "tertiary-container": "#3c3c37",
                    "inverse-on-surface": "#f3f0f0",
                    "primary-fixed-dim": "#a5d0b9",
                    "on-tertiary-container": "#a8a6a0",
                    "tertiary-fixed": "#e5e2db",
                    "on-error": "#ffffff",
                    "surface-container-high": "#eae7e7",
                    "on-surface": "#1b1c1c",
                    "secondary-fixed-dim": "#e9c176",
                    "on-primary": "#ffffff",
                    "primary": "#012d1d",
                    "surface-tint": "#3f6653",
                    "error": "#ba1a1a",
                    "primary-fixed": "#c1ecd4",
                    "surface-container-highest": "#e4e2e1",
                    "on-surface-variant": "#414844",
                    "background": "#fcf9f8",
                    "secondary-fixed": "#ffdea5",
                    "surface-bright": "#fcf9f8",
                    "on-primary-fixed": "#002114",
                    "secondary": "#775a19",
                    "on-secondary-fixed": "#261900",
                    "on-tertiary-fixed-variant": "#474742",
                    "inverse-primary": "#a5d0b9",
                    "outline": "#717973"
            },
            "borderRadius": {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "full": "9999px"
            },
            "spacing": {
                    "base": "8px",
                    "stack-lg": "48px",
                    "stack-md": "24px",
                    "stack-sm": "12px",
                    "gutter": "24px",
                    "section-gap": "120px",
                    "margin-mobile": "16px",
                    "container-max": "1280px"
            },
            "fontFamily": {
                    "headline-lg": [
                            "Public Sans"
                    ],
                    "body-lg": [
                            "Public Sans"
                    ],
                    "label-md": [
                            "Public Sans"
                    ],
                    "headline-sm": [
                            "Public Sans"
                    ],
                    "headline-md": [
                            "Public Sans"
                    ],
                    "body-md": [
                            "Public Sans"
                    ],
                    "display-xl": [
                            "Public Sans"
                    ]
            },
            "fontSize": {
                    "headline-lg": [
                            "48px",
                            {
                                    "lineHeight": "1.2",
                                    "letterSpacing": "-0.01em",
                                    "fontWeight": "700"
                            }
                    ],
                    "body-lg": [
                            "18px",
                            {
                                    "lineHeight": "1.6",
                                    "fontWeight": "400"
                            }
                    ],
                    "label-md": [
                            "14px",
                            {
                                    "lineHeight": "1.2",
                                    "letterSpacing": "0.05em",
                                    "fontWeight": "600"
                            }
                    ],
                    "headline-sm": [
                            "24px",
                            {
                                    "lineHeight": "1.4",
                                    "fontWeight": "600"
                            }
                    ],
                    "headline-md": [
                            "32px",
                            {
                                    "lineHeight": "1.3",
                                    "fontWeight": "600"
                            }
                    ],
                    "body-md": [
                            "16px",
                            {
                                    "lineHeight": "1.6",
                                    "fontWeight": "400"
                            }
                    ],
                    "display-xl": [
                            "72px",
                            {
                                    "lineHeight": "1.1",
                                    "letterSpacing": "-0.02em",
                                    "fontWeight": "700"
                            }
                    ]
            }
    },
        },
      }
    </script>
<style>
        body { font-family: 'Public Sans', sans-serif; }
    </style>
</head>
<body class="bg-background text-on-background font-body-lg antialiased">
<nav class="bg-white dark:bg-emerald-950 fixed top-0 w-full z-50 h-20 border-b border-gray-200 dark:border-emerald-900 flat no shadows font-['Public_Sans'] tracking-tight text-sm font-medium">
<div class="flex justify-between items-center max-w-7xl mx-auto px-6 lg:px-24 w-full h-full">
<div class="text-xl font-bold text-emerald-900 dark:text-white uppercase tracking-wider">
                Committee of Agriculture
            </div>
<div class="hidden md:flex space-x-6 items-center">
<a class="text-slate-600 dark:text-slate-400 hover:text-emerald-800 dark:hover:text-emerald-200 hover:bg-slate-50 dark:hover:bg-emerald-900/50 transition-all duration-200 active:scale-95 cursor-pointer px-3 py-2 rounded-md" href="#">Agriculture</a>
<a class="text-slate-600 dark:text-slate-400 hover:text-emerald-800 dark:hover:text-emerald-200 hover:bg-slate-50 dark:hover:bg-emerald-900/50 transition-all duration-200 active:scale-95 cursor-pointer px-3 py-2 rounded-md" href="#">Fishery</a>
<a class="text-slate-600 dark:text-slate-400 hover:text-emerald-800 dark:hover:text-emerald-200 hover:bg-slate-50 dark:hover:bg-emerald-900/50 transition-all duration-200 active:scale-95 cursor-pointer px-3 py-2 rounded-md" href="#">Technical Supervision</a>
<a class="text-slate-600 dark:text-slate-400 hover:text-emerald-800 dark:hover:text-emerald-200 hover:bg-slate-50 dark:hover:bg-emerald-900/50 transition-all duration-200 active:scale-95 cursor-pointer px-3 py-2 rounded-md" href="#">Programs</a>
<a class="text-slate-600 dark:text-slate-400 hover:text-emerald-800 dark:hover:text-emerald-200 hover:bg-slate-50 dark:hover:bg-emerald-900/50 transition-all duration-200 active:scale-95 cursor-pointer px-3 py-2 rounded-md" href="#">Documents</a>
<a class="text-slate-600 dark:text-slate-400 hover:text-emerald-800 dark:hover:text-emerald-200 hover:bg-slate-50 dark:hover:bg-emerald-900/50 transition-all duration-200 active:scale-95 cursor-pointer px-3 py-2 rounded-md" href="#">Services</a>
</div>
<button class="hidden md:flex bg-secondary-container text-on-secondary-container hover:bg-secondary-fixed font-label-md text-label-md px-6 py-3 rounded-DEFAULT transition-all duration-200 active:scale-95 cursor-pointer">
                Apply for Grant
            </button>
</div>
</nav>
<main class="pt-20">
<section class="relative w-full min-h-[819px] flex items-center justify-center overflow-hidden">
<div class="absolute inset-0 z-0">
<img alt="Volgograd Agriculture Fields" class="w-full h-full object-cover" data-alt="wide panoramic view of golden wheat fields stretching to the horizon in Volgograd under a clear blue sky, modern agricultural combines harvesting in the distance, warm bright sunlight" src="https://lh3.googleusercontent.com/aida-public/AB6AXuACKqpnlTPoNj3U6UR33X72XE9euFYb4CenyvHWb-FRgs41Zo1I_7epi96KZmHhoL33QnSU2v7Po0iaWO0Nn7ckGPA6-cnmHpEFFJkc_p8dCwYgcIUgdxZ9XLifAealYwFSs0D9ueSzjlYxkEQ45xh7He0WMuGOk33oGB2ClsZzV5hGjlzZWaPpuelt68NKl2Z66KTJ-dhERLWBWC7352rpVqG00uVah2SDWzY8196fEhjha2BcqSjC57NHpHmPTq0fBJVixdOfUx5D"/>
<div class="absolute inset-0 bg-primary/40 mix-blend-multiply"></div>
<div class="absolute inset-0 bg-gradient-to-t from-background via-background/20 to-transparent"></div>
</div>
<div class="relative z-10 max-w-container-max mx-auto px-gutter w-full mt-stack-lg">
<div class="max-w-3xl">
<span class="inline-block px-4 py-1.5 mb-stack-sm rounded-full bg-secondary-container text-on-secondary-container font-label-md text-label-md">
                        Institutional Stability &amp; Modern Progress
                    </span>
<h1 class="font-display-xl text-display-xl text-on-primary mb-stack-md leading-tight">
                        Cultivating the Future of Volgograd Region
                    </h1>
<p class="font-body-lg text-body-lg text-surface-container-high mb-stack-lg max-w-2xl">
                        Empowering farmers, ensuring food security, and driving technological innovation across our agricultural and fishery sectors.
                    </p>
<div class="flex gap-stack-sm">
<button class="bg-primary text-on-primary hover:bg-primary-fixed-variant px-8 py-4 rounded-DEFAULT font-label-md text-label-md transition-colors shadow-sm">
                            Explore Programs
                        </button>
<button class="bg-surface text-primary border border-outline hover:bg-surface-container px-8 py-4 rounded-DEFAULT font-label-md text-label-md transition-colors">
                            Latest Reports
                        </button>
</div>
</div>
</div>
</section>
<section class="max-w-container-max mx-auto px-gutter mt-stack-lg mb-section-gap">
<div class="grid grid-cols-1 md:grid-cols-4 gap-gutter">
<a class="group bg-surface-container hover:bg-surface-container-high border border-outline-variant rounded-xl p-stack-md transition-all duration-300 hover:shadow-sm" href="#">
<div class="w-12 h-12 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center mb-stack-sm">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">description</span>
</div>
<h3 class="font-headline-sm text-headline-sm text-on-surface mb-2">Grants</h3>
<p class="font-body-md text-body-md text-on-surface-variant">Access financial support and subsidies for agribusiness development.</p>
</a>
<a class="group bg-surface-container hover:bg-surface-container-high border border-outline-variant rounded-xl p-stack-md transition-all duration-300 hover:shadow-sm" href="#">
<div class="w-12 h-12 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center mb-stack-sm">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">landscape</span>
</div>
<h3 class="font-headline-sm text-headline-sm text-on-surface mb-2">Land Planning</h3>
<p class="font-body-md text-body-md text-on-surface-variant">Resources for soil conservation and optimal land utilization.</p>
</a>
<a class="group bg-surface-container hover:bg-surface-container-high border border-outline-variant rounded-xl p-stack-md transition-all duration-300 hover:shadow-sm" href="#">
<div class="w-12 h-12 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center mb-stack-sm">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">engineering</span>
</div>
<h3 class="font-headline-sm text-headline-sm text-on-surface mb-2">Technical Supervision</h3>
<p class="font-body-md text-body-md text-on-surface-variant">Machinery registration, safety checks, and compliance standards.</p>
</a>
<a class="group bg-surface-container hover:bg-surface-container-high border border-outline-variant rounded-xl p-stack-md transition-all duration-300 hover:shadow-sm" href="#">
<div class="w-12 h-12 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center mb-stack-sm">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">water</span>
</div>
<h3 class="font-headline-sm text-headline-sm text-on-surface mb-2">Fishery Quotas</h3>
<p class="font-body-md text-body-md text-on-surface-variant">Manage aquaculture resources and biological resource allocations.</p>
</a>
</div>
</section>
<section class="bg-surface-container-low py-section-gap border-y border-outline-variant">
<div class="max-w-container-max mx-auto px-gutter">
<div class="flex flex-col md:flex-row justify-between items-end mb-stack-lg gap-stack-md">
<div class="max-w-2xl">
<h2 class="font-headline-lg text-headline-lg text-on-surface mb-stack-sm">Strategic Directions</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant">Guiding principles and core operational areas to ensure sustainable growth and institutional reliability.</p>
</div>
<button class="flex items-center gap-2 text-primary font-label-md text-label-md hover:text-on-primary-fixed-variant transition-colors">
                        View Full Strategy <span class="material-symbols-outlined">arrow_forward</span>
</button>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
<div class="bg-surface rounded-[24px] overflow-hidden border border-outline-variant flex flex-col h-full">
<div class="h-64 relative">
<img alt="Agriculture Machinery" class="w-full h-full object-cover" data-alt="close up view of a modern green tractor tilling fertile dark soil in a vast agricultural field, bright sunny day, sharp focus on the heavy machinery blades" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDsFLhvSeGmtAut-udtavfgxUWaoqgKjLvVFIfryOIfFaZq3JLWvLt5poTtnEkq-V0Q4xRetIFHxTxIFWOPGT78MrN8oE9IMsEeffPyT2UQQPZUOCijqrG8xqVoNvpleV5heC5Nrlk99pKVK0mH3aQZFsq94exBmhBwb2agkVe-CTOifKblwiV4Gg4BKaKBSwRyPLrW_kNZ62vkVuM03zvlk4Js2QayoweeUDvyRJn9U2FylyL8lCk1EP2TbdztXcQJDCivRzH0_lJ3"/>
</div>
<div class="p-stack-md flex-grow flex flex-col">
<span class="text-secondary font-label-md text-label-md mb-2 block uppercase tracking-wider">Sector One</span>
<h3 class="font-headline-md text-headline-md text-on-surface mb-stack-sm">Agriculture</h3>
<p class="font-body-md text-body-md text-on-surface-variant mb-stack-md flex-grow">
                                Advancing seeding technologies, livestock management, and maintaining the financial health of regional farming enterprises through systematic support.
                            </p>
<a class="inline-flex items-center gap-2 text-primary font-label-md text-label-md hover:underline" href="#">
                                Explore Initiatives <span class="material-symbols-outlined text-sm">chevron_right</span>
</a>
</div>
</div>
<div class="bg-surface rounded-[24px] overflow-hidden border border-outline-variant flex flex-col h-full">
<div class="h-64 relative">
<img alt="Aquaculture Facility" class="w-full h-full object-cover" data-alt="industrial sturgeon farming facility indoors, large circular concrete water tanks with circulating clean water, bright industrial overhead lighting, professional aquaculture setup" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCKPNE1zmE9-pUH3p1xv1NVX8SVwn-7IDq1y-6fF0UT_V3huoErX78gUKJoMn_lBqEnK4FGQ6fkBcVkSljQEUsA9nbatzJGBdB0L6ZQTbXy7CFfMYfp_ya0Vd5M22JUvs-oIVTyAz5JXXOoc3-7o3HNSOAejFBMFG2Nn7GReOrn8qIi_vE-S314UlIsuplj6e0qOtMhwFLUFr7yxQPbaB_Ib15ziYnNFcddzeR4iL-TQqrm-OzpNkiqPqXJTOjjFFuQ_3C83wI22J4c"/>
</div>
<div class="p-stack-md flex-grow flex flex-col">
<span class="text-secondary font-label-md text-label-md mb-2 block uppercase tracking-wider">Sector Two</span>
<h3 class="font-headline-md text-headline-md text-on-surface mb-stack-sm">Fishery &amp; Aquaculture</h3>
<p class="font-body-md text-body-md text-on-surface-variant mb-stack-md flex-grow">
                                Preserving biological resources, strict water management, and expanding commercial aquaculture capabilities across the region's waterways.
                            </p>
<a class="inline-flex items-center gap-2 text-primary font-label-md text-label-md hover:underline" href="#">
                                Explore Initiatives <span class="material-symbols-outlined text-sm">chevron_right</span>
</a>
</div>
</div>
<div class="bg-surface rounded-[24px] overflow-hidden border border-outline-variant flex flex-col h-full">
<div class="h-64 relative">
<img alt="Technical Inspection" class="w-full h-full object-cover" data-alt="professional inspector in high visibility vest examining heavy agricultural machinery in a bright workshop, holding a digital tablet, shallow depth of field" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCOOWINDZES5nR5S0mlr7VZK_7IdcOR-IIhHdm19zNyVbDo7t3L8V8nGwX9BAGIx5sYrvWkxQY_7U8-TMWKxUMz5jvwyCqYAmIgs1_lCPVQGN76Y7FvQMen0jZUAQhEmGT9NjfYQ44pEb_LPuSPP8NxighVKZ5fAtISUPczTpLKP7WpAmbhxhXnTfX69I09odyBAbJmU_Xteu6jpp6fklzn5AuPES8lCX_mO_thBOLAr442jaLfUOKypSjOQQcl2jWXlTqoulliFSd7"/>
</div>
<div class="p-stack-md flex-grow flex flex-col">
<span class="text-secondary font-label-md text-label-md mb-2 block uppercase tracking-wider">Sector Three</span>
<h3 class="font-headline-md text-headline-md text-on-surface mb-stack-sm">State Supervision</h3>
<p class="font-body-md text-body-md text-on-surface-variant mb-stack-md flex-grow">
                                Rigorous machinery inspections, attractions control, and enforcement of operational standards to guarantee safety and compliance.
                            </p>
<a class="inline-flex items-center gap-2 text-primary font-label-md text-label-md hover:underline" href="#">
                                Explore Initiatives <span class="material-symbols-outlined text-sm">chevron_right</span>
</a>
</div>
</div>
</div>
</div>
</section>
</main>
<footer class="bg-slate-50 dark:bg-slate-900 w-full py-16 border-t border-gray-200 dark:border-slate-800 flat no shadows font-['Public_Sans'] text-sm leading-relaxed text-emerald-900 dark:text-emerald-100">
<div class="grid grid-cols-1 md:grid-cols-12 gap-8 max-w-7xl mx-auto px-6 lg:px-24">
<div class="md:col-span-4">
<div class="text-lg font-bold text-emerald-900 dark:text-emerald-50 mb-4">
                    Committee of Agriculture
                </div>
<p class="text-slate-500 dark:text-slate-400 mb-6 max-w-xs">
                    Institutional Stability &amp; Modern Progress. Ensuring the growth and security of the Volgograd Region's agricultural sector.
                </p>
<div class="text-slate-500 dark:text-slate-400">
                    © 2024 Committee of Agriculture. Institutional Stability &amp; Modern Progress.
                </div>
</div>
<div class="md:col-span-8 flex flex-wrap gap-8 justify-end">
<div class="flex flex-col gap-3">
<a class="text-slate-500 dark:text-slate-400 hover:text-emerald-700 dark:hover:text-emerald-300 hover:underline transition-all cursor-pointer" href="#">Anti-Corruption</a>
<a class="text-slate-500 dark:text-slate-400 hover:text-emerald-700 dark:hover:text-emerald-300 hover:underline transition-all cursor-pointer" href="#">Open Data</a>
<a class="text-slate-500 dark:text-slate-400 hover:text-emerald-700 dark:hover:text-emerald-300 hover:underline transition-all cursor-pointer" href="#">Privacy Policy</a>
<a class="text-slate-500 dark:text-slate-400 hover:text-emerald-700 dark:hover:text-emerald-300 hover:underline transition-all cursor-pointer" href="#">Contact Us</a>
<a class="text-slate-500 dark:text-slate-400 hover:text-emerald-700 dark:hover:text-emerald-300 hover:underline transition-all cursor-pointer" href="#">Sitemap</a>
</div>
</div>
</div>
</footer>
</body></html>

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- livewire/livewire (LIVEWIRE) - v4
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12

## Skills Activation

This project has domain-specific skills available. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

- `laravel-best-practices` — Apply this skill whenever writing, reviewing, or refactoring Laravel PHP code. This includes creating or modifying controllers, models, migrations, form requests, policies, jobs, scheduled commands, service classes, and Eloquent queries. Triggers for N+1 and query performance issues, caching strategies, authorization and security patterns, validation, error handling, queue and job configuration, route definitions, and architectural decisions. Also use for Laravel code reviews and refactoring existing Laravel code to follow best practices. Covers any task involving Laravel backend PHP code patterns.
- `livewire-development` — Use for any task or question involving Livewire. Activate if user mentions Livewire, wire: directives, or Livewire-specific concepts like wire:model, wire:click, wire:sort, or islands, invoke this skill. Covers building new components, debugging reactivity issues, real-time form validation, drag-and-drop, loading states, migrating from Livewire 3 to 4, converting component formats (SFC/MFC/class-based), and performance optimization. Do not use for non-Livewire reactive UI (React, Vue, Alpine-only, Inertia.js) or standard Laravel forms without Livewire.
- `pest-testing` — Use this skill for Pest PHP testing in Laravel projects only. Trigger whenever any test is being written, edited, fixed, or refactored — including fixing tests that broke after a code change, adding assertions, converting PHPUnit to Pest, adding datasets, and TDD workflows. Always activate when the user asks how to write something in Pest, mentions test files or directories (tests/Feature, tests/Unit, tests/Browser), or needs browser testing, smoke testing multiple pages for JS errors, or architecture tests. Covers: test()/it()/expect() syntax, datasets, mocking, browser testing (visit/click/fill), smoke testing, arch(), Livewire component tests, RefreshDatabase, and all Pest 4 features. Do not use for factories, seeders, migrations, controllers, models, or non-test PHP code.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.
- To check environment variables, read the `.env` file directly.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== livewire/core rules ===

# Livewire

- Livewire allow to build dynamic, reactive interfaces in PHP without writing JavaScript.
- You can use Alpine.js for client-side interactions instead of JavaScript frameworks.
- Keep state server-side so the UI reflects it. Validate and authorize in actions as you would in HTTP requests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

</laravel-boost-guidelines>

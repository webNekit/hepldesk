<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Footer;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Header;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class DirectoryTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'directory-table';

    public function setUp(): array
    {
        return [
            (new Exportable('export'))
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            (new Header())->showSearchInput(),
            (new Footer())
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return User::query()->with('department');
    }

    public function relationSearch(): array
    {
        return [
            'department' => [
                'name',
            ],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('email')
            ->add('department_name', fn (User $model) => optional($model->department)->name)
            ->add('position')
            ->add('phone')
            ->add('cabinet');
    }

    public function columns(): array
    {
        return [
            Column::make('ФИО', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Email', 'email')
                ->sortable()
                ->searchable(),

            Column::make('Отдел', 'department_name'),

            Column::make('Должность', 'position')
                ->sortable()
                ->searchable(),

            Column::make('Телефон', 'phone')
                ->sortable()
                ->searchable(),

            Column::make('Кабинет', 'cabinet')
                ->sortable()
                ->searchable(),
        ];
    }
}

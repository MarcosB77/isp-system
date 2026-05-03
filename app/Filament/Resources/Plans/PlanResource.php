<?php
namespace App\Filament\Resources\Plans;

use App\Domains\Billing\Models\Plan;
use App\Filament\Resources\Plans\Pages\CreatePlan;
use App\Filament\Resources\Plans\Pages\EditPlan;
use App\Filament\Resources\Plans\Pages\ListPlans;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = "Planos";
    protected static ?string $modelLabel = "Plano";
    protected static ?string $pluralModelLabel = "Planos";
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make("Dados do Plano")->columns(2)->schema([
                TextInput::make("name")->label("Nome do plano")->required()->maxLength(255),
                Select::make("technology")->label("Tecnologia")
                    ->options(["fibra" => "Fibra", "radio" => "Rádio", "satelite" => "Satélite"])
                    ->required(),
                TextInput::make("speed_download")->label("Download (Mbps)")->numeric()->required(),
                TextInput::make("speed_upload")->label("Upload (Mbps)")->numeric()->required(),
                TextInput::make("price")->label("Preço (R$)")->numeric()->prefix("R$")->required(),
                Toggle::make("active")->label("Ativo")->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("name")->label("Nome")->searchable()->sortable(),
                TextColumn::make("technology")->label("Tecnologia")->badge(),
                TextColumn::make("speed_download")->label("Download")->suffix(" Mbps"),
                TextColumn::make("speed_upload")->label("Upload")->suffix(" Mbps"),
                TextColumn::make("price")->label("Preço")->money("BRL")->sortable(),
                IconColumn::make("active")->label("Ativo")->boolean(),
            ])
            ->actions([
                EditAction::make()->label("Editar"),
                DeleteAction::make()->label("Excluir"),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            "index"  => ListPlans::route("/"),
            "create" => CreatePlan::route("/create"),
            "edit"   => EditPlan::route("/{record}/edit"),
        ];
    }
}

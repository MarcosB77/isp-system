<?php
namespace App\Filament\Resources\Clients;

use App\Domains\Client\Models\Client;
use App\Filament\Resources\Clients\Pages\CreateClient;
use App\Filament\Resources\Clients\Pages\EditClient;
use App\Filament\Resources\Clients\Pages\ListClients;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;
    protected static ?string $navigationLabel = "Clientes";
    protected static ?string $modelLabel = "Cliente";
    protected static ?string $pluralModelLabel = "Clientes";
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = "name";

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make("Dados Pessoais")->columns(2)->schema([
                TextInput::make("name")->label("Nome completo")->required()->maxLength(255),
                TextInput::make("email")->label("E-mail")->email()->required()->unique(ignoreRecord: true),
                TextInput::make("cpf")->label("CPF")->required()->unique(ignoreRecord: true),
                TextInput::make("phone")->label("Telefone")->tel(),
            ]),
            Section::make("Endereço")->columns(3)->schema([
                TextInput::make("address")->label("Endereço")->columnSpan(2),
                TextInput::make("city")->label("Cidade"),
                TextInput::make("state")->label("UF"),
            ]),
           Section::make("Plano e Contrato")->columns(2)->schema([
                Select::make("plan_id")
                    ->label("Plano de Internet")
                    ->options(\App\Domains\Billing\Models\Plan::where("active", true)->pluck("name", "id"))
                    ->searchable(),
                TextInput::make("due_day")
                    ->label("Dia de vencimento")
                    ->numeric()
                    ->default(10),
            ]),
            Section::make("Status")->schema([
                Select::make("status")->label("Status")
                    ->options(["active" => "Ativo", "suspended" => "Suspenso", "cancelled" => "Cancelado"])
                    ->default("active")->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("id")->label("ID")->sortable(),
                TextColumn::make("name")->label("Nome")->searchable()->sortable(),
                TextColumn::make("email")->label("E-mail")->searchable(),
                TextColumn::make("phone")->label("Telefone")->default("—"),
                TextColumn::make("city")->label("Cidade")->default("—"),
                TextColumn::make("status")->label("Status")->badge()
                    ->color(fn (string $state): string => match ($state) {
                        "active" => "success", "suspended" => "warning", "cancelled" => "danger", default => "gray",
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        "active" => "Ativo", "suspended" => "Suspenso", "cancelled" => "Cancelado", default => $state,
                    }),
                TextColumn::make("plan.name")->label("Plano")->default("—"),
                TextColumn::make("created_at")->label("Cadastrado em")->dateTime("d/m/Y")->sortable(),
            ])
            ->filters([
                SelectFilter::make("status")->label("Status")
                    ->options(["active" => "Ativo", "suspended" => "Suspenso", "cancelled" => "Cancelado"]),
                TrashedFilter::make(),
            ])
            ->actions([
                \Filament\Actions\EditAction::make()->label("Editar"),
                \Filament\Actions\DeleteAction::make()->label("Excluir"),
                \Filament\Actions\RestoreAction::make()->label("Restaurar"),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                    \Filament\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort("created_at", "desc");
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            "index"  => ListClients::route("/"),
            "create" => CreateClient::route("/create"),
            "edit"   => EditClient::route("/{record}/edit"),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}

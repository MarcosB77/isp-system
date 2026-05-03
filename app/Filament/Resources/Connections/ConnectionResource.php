<?php
namespace App\Filament\Resources\Connections;

use App\Domains\Network\Models\Connection;
use App\Domains\Client\Services\ClientService;
use App\Filament\Resources\Connections\Pages\CreateConnection;
use App\Filament\Resources\Connections\Pages\EditConnection;
use App\Filament\Resources\Connections\Pages\ListConnections;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class ConnectionResource extends Resource
{
    protected static ?string $model = Connection::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWifi;
    protected static ?string $navigationLabel = "Conexões";
    protected static ?string $modelLabel = "Conexão";
    protected static ?string $pluralModelLabel = "Conexões";
    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make("Dados da Conexão")->columns(2)->schema([
                \Filament\Forms\Components\Select::make("client_id")
                    ->label("Cliente")
                    ->options(\App\Domains\Client\Models\Client::pluck("name", "id"))
                    ->searchable()
                    ->required(),
                TextInput::make("ip_address")->label("Endereço IP")->placeholder("192.168.1.100"),
                TextInput::make("pppoe_username")->label("Usuário PPPoE")->required(),
                TextInput::make("pppoe_password")->label("Senha PPPoE")->password()->revealable(),
                TextInput::make("mac_address")->label("MAC Address")->placeholder("AA:BB:CC:DD:EE:FF"),
                TextInput::make("onu_serial")->label("Serial ONU"),
                Toggle::make("online")->label("Online")->default(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("client.name")->label("Cliente")->searchable()->sortable(),
                TextColumn::make("pppoe_username")->label("Usuário PPPoE")->searchable()->copyable(),
                TextColumn::make("ip_address")->label("IP")->default("—")->copyable(),
                TextColumn::make("mac_address")->label("MAC")->default("—"),
                TextColumn::make("onu_serial")->label("ONU")->default("—"),
                IconColumn::make("online")->label("Online")->boolean()
                    ->trueColor("success")->falseColor("danger"),
            ])
            ->filters([
                SelectFilter::make("online")->label("Status")
                    ->options(["1" => "Online", "0" => "Offline"]),
            ])
            ->actions([
                EditAction::make()->label("Editar"),
                Action::make("suspend")
                    ->label("Suspender")
                    ->icon("heroicon-o-no-symbol")
                    ->color("warning")
                    ->requiresConfirmation()
                    ->modalHeading("Suspender conexão")
                    ->modalDescription("Isso vai bloquear o acesso do cliente no Mikrotik.")
                    ->action(function (Connection $record) {
                        app(\App\Infrastructure\External\MikrotikService::class)
                            ->suspendUser($record->pppoe_username);
                        $record->client->update(["status" => "suspended"]);
                        $record->update(["online" => false]);
                    }),
                Action::make("activate")
                    ->label("Reativar")
                    ->icon("heroicon-o-check-circle")
                    ->color("success")
                    ->requiresConfirmation()
                    ->modalHeading("Reativar conexão")
                    ->modalDescription("Isso vai liberar o acesso do cliente no Mikrotik.")
                    ->action(function (Connection $record) {
                        app(\App\Infrastructure\External\MikrotikService::class)
                            ->activateUser($record->pppoe_username);
                        $record->client->update(["status" => "active"]);
                        $record->update(["online" => true]);
                    }),
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
            "index"  => ListConnections::route("/"),
            "create" => CreateConnection::route("/create"),
            "edit"   => EditConnection::route("/{record}/edit"),
        ];
    }
}

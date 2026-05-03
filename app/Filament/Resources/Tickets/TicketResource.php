<?php
namespace App\Filament\Resources\Tickets;

use App\Domains\Support\Models\Ticket;
use App\Domains\Client\Models\Client;
use App\Filament\Resources\Tickets\Pages\CreateTicket;
use App\Filament\Resources\Tickets\Pages\EditTicket;
use App\Filament\Resources\Tickets\Pages\ListTickets;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;
    protected static ?string $navigationLabel = "Chamados";
    protected static ?string $modelLabel = "Chamado";
    protected static ?string $pluralModelLabel = "Chamados";
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make("Chamado")->columns(2)->schema([
                Select::make("client_id")->label("Cliente")
                    ->options(\App\Domains\Client\Models\Client::pluck("name", "id"))->searchable()->required(),
                TextInput::make("subject")->label("Assunto")->required()->columnSpanFull(),
                Textarea::make("description")->label("Descrição")->required()->columnSpanFull()->rows(4),
                Select::make("priority")->label("Prioridade")
                    ->options([
                        "low"      => "Baixa",
                        "medium"   => "Média",
                        "high"     => "Alta",
                        "critical" => "Crítica",
                    ])->default("medium"),
                Select::make("status")->label("Status")
                    ->options([
                        "open"        => "Aberto",
                        "in_progress" => "Em andamento",
                        "resolved"    => "Resolvido",
                        "closed"      => "Fechado",
                    ])->default("open"),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("client.name")->label("Cliente")->searchable()->sortable(),
                TextColumn::make("subject")->label("Assunto")->limit(40)->searchable(),
                TextColumn::make("priority")->label("Prioridade")->badge()
                    ->color(fn (string $state): string => match ($state) {
                        "critical" => "danger",
                        "high"     => "warning",
                        "medium"   => "info",
                        "low"      => "gray",
                        default    => "gray",
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        "critical" => "Crítica",
                        "high"     => "Alta",
                        "medium"   => "Média",
                        "low"      => "Baixa",
                        default    => $state,
                    }),
                TextColumn::make("status")->label("Status")->badge()
                    ->color(fn (string $state): string => match ($state) {
                        "open"        => "warning",
                        "in_progress" => "info",
                        "resolved"    => "success",
                        "closed"      => "gray",
                        default       => "gray",
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        "open"        => "Aberto",
                        "in_progress" => "Em andamento",
                        "resolved"    => "Resolvido",
                        "closed"      => "Fechado",
                        default       => $state,
                    }),
                TextColumn::make("created_at")->label("Aberto em")->dateTime("d/m/Y")->sortable(),
            ])
            ->actions([
                EditAction::make()->label("Editar"),
                DeleteAction::make()->label("Excluir"),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort("created_at", "desc");
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            "index"  => ListTickets::route("/"),
            "create" => CreateTicket::route("/create"),
            "edit"   => EditTicket::route("/{record}/edit"),
        ];
    }
}

<?php
namespace App\Filament\Resources\Invoices;

use App\Domains\Billing\Models\Invoice;
use App\Domains\Client\Models\Client;
use App\Filament\Resources\Invoices\Pages\CreateInvoice;
use App\Filament\Resources\Invoices\Pages\EditInvoice;
use App\Filament\Resources\Invoices\Pages\ListInvoices;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;
    protected static ?string $navigationLabel = "Faturas";
    protected static ?string $modelLabel = "Fatura";
    protected static ?string $pluralModelLabel = "Faturas";
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make("Dados da Fatura")->columns(2)->schema([
                Select::make("client_id")->label("Cliente")
                ->options(Client::pluck("name", "id"))
                ->searchable()
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {
        if (!$state) return;
        $client = \App\Domains\Client\Models\Client::with("plan")->find($state);
        if ($client && $client->plan) {
            $set("amount", $client->plan->price);
            $set("due_date", now()->day($client->due_day ?? 10)->format("Y-m-d"));
        }
    }),
                TextInput::make("amount")->label("Valor")->numeric()->prefix("R$")->required(),
                DatePicker::make("due_date")->label("Vencimento")->required(),
                Select::make("status")->label("Status")
                    ->options([
                        "pending"   => "Pendente",
                        "paid"      => "Pago",
                        "overdue"   => "Vencido",
                        "cancelled" => "Cancelado",
                    ])->default("pending")->required(),
                Select::make("payment_method")->label("Forma de pagamento")
                    ->options([
                        "pix"      => "Pix",
                        "boleto"   => "Boleto",
                        "cartao"   => "Cartao",
                        "dinheiro" => "Dinheiro",
                    ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("client.name")->label("Cliente")->searchable()->sortable(),
                TextColumn::make("amount")->label("Valor")->money("BRL")->sortable(),
                TextColumn::make("due_date")->label("Vencimento")->date("d/m/Y")->sortable(),
                TextColumn::make("status")->label("Status")->badge()
                    ->color(fn (string $state): string => match ($state) {
                        "paid"    => "success",
                        "pending" => "warning",
                        "overdue" => "danger",
                        default   => "gray",
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        "paid"      => "Pago",
                        "pending"   => "Pendente",
                        "overdue"   => "Vencido",
                        "cancelled" => "Cancelado",
                        default     => $state,
                    }),
                TextColumn::make("payment_method")->label("Pagamento")->default("—"),
                TextColumn::make("paid_at")->label("Pago em")->date("d/m/Y")->placeholder("Não pago"),
            ])
            ->filters([
                SelectFilter::make("status")->label("Status")
                    ->options([
                        "pending"   => "Pendente",
                        "paid"      => "Pago",
                        "overdue"   => "Vencido",
                        "cancelled" => "Cancelado",
                    ]),
            ])
            ->actions([
                EditAction::make()->label("Editar"),
                Action::make("pay")
                    ->label("Pagar")
                    ->icon("heroicon-o-banknotes")
                    ->color("success")
                    ->visible(fn (Invoice $record): bool => $record->status !== "paid")
                    ->requiresConfirmation()
                    ->modalHeading("Registrar pagamento")
                    ->modalDescription("Confirma o pagamento desta fatura via Pix?")
                    ->action(fn (Invoice $record) => $record->markAsPaid("pix")),
                DeleteAction::make()->label("Excluir"),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort("due_date", "desc");
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            "index"  => ListInvoices::route("/"),
            "create" => CreateInvoice::route("/create"),
            "edit"   => EditInvoice::route("/{record}/edit"),
        ];
    }
}

# Registrar TicketPolicy

Após aplicar o overlay, registre a policy.

Abra `app/Providers/AppServiceProvider.php` (ou AuthServiceProvider, se existir) e adicione:

```php
use App\Models\Ticket;
use App\Domain\Tickets\Policies\TicketPolicy;
use Illuminate\Support\Facades\Gate;

public function boot(): void
{
    Gate::policy(Ticket::class, TicketPolicy::class);
}
```

(Se você já tem um `boot()` com outras coisas, só inclua a linha `Gate::policy(...)`.)

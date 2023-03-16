# Uffff

# Filter unicode user input

Properly processing unicode user input is surprisingly tricky:

-   Ensuring left-to-right markers are closed properly
-   Trimming whitespaces and handling esoteric unicode whitespaces well
-   Normalizing unicode equivalent characters to a well-known form
-   Harmonizing newlines to a single format

## High-level API

```php
Ufff\unicode(string $value): string
```

The idea is to use it directly in an entity, e.g.

```php
use Uffff\unicode;

class Entity
{
    private string $name;

    public __construct(string $name): void
    {
        $this->name = unicode($name);
    }
}
```

If the field could be nullable, there is an equivalent `_or_null` function.

```php
Ufff\unicode_or_null(?string $value): ?string
```

```php
use Uffff\unicode_or_null;

class Entity
{
    private ?string $optional;

    public __construct(?string $optional = null): void
    {
        $this->name = unicode_or_null($optional);
    }
}
```

# Uffff - _Unicode input processing made trivial_!

[![CI](https://github.com/lstrojny/uffff/actions/workflows/ci.yml/badge.svg)](https://github.com/lstrojny/uffff/actions/workflows/ci.yml) [![Documentation Status](https://readthedocs.org/projects/uffff/badge/?version=latest)](https://uffff.readthedocs.io/en/latest/?badge=latest)

Properly processing unicode user input is surprisingly tricky:

-   Ensuring bidirectional markers are balanced properly
-   Trimming whitespaces and handling esoteric unicode whitespaces well
-   Normalizing unicode equivalent characters to a well-known form
-   Harmonizing newlines to a single format
-   … and more

With **Uffff** the problem is reduced to:

```php
$good = Uffff\unicode($bad);
```

[Read the docs](https://uffff.readthedocs.io/) to learn more.

## Development

Use `nix develop` to initialize the development environment defined in `flake.nix`.
To select a specific version, use:

-   `nix develop ".#php83"` to select PHP 8.3
-   `nix develop ".#php82"` to select PHP 8.2

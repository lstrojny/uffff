# Uffff - Filter unicode user input
[![CI](https://github.com/lstrojny/uffff/actions/workflows/ci.yml/badge.svg)](https://github.com/lstrojny/uffff/actions/workflows/ci.yml) [![Documentation Status](https://readthedocs.org/projects/uffff/badge/?version=latest)](https://uffff.readthedocs.io/en/latest/?badge=latest)

Properly processing unicode user input is surprisingly tricky:

-   Ensuring bidirectional markers are balanced properly
-   Trimming whitespaces and handling esoteric unicode whitespaces well
-   Normalizing unicode equivalent characters to a well-known form
-   Harmonizing newlines to a single format
-   … and more

With **Uffff** the problem is reduced to:

```php
$good = Ufff\unicode($bad);
```

[Read the docs](https://uffff.readthedocs.io/) for learn more
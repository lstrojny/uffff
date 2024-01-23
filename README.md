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

### Setting up the environment

_Uffff_ uses [direnv](https://direnv.net/) to set up the development environment. Run `direnv allow` to initialize the
development environment.

To switch to a different PHP version for development or if you prefer not to use direnv, you can use `nix develop` to
initialize the environment:

-   `nix develop github:loophp/nix-shell#env-php82` to select PHP 8.2
-   `nix develop github:loophp/nix-shell#env-php83` to select PHP 8.3
-   `nix develop github:loophp/nix-sphinx` to set up sphinx to build documentation

### Making changes

Change the code and then run `composer check` to run tests, static inspection, everything and the kitchen sink. Once
that succeeds, open a pull request.

Edit the documentation in `docs/` and run `composer docs` to build the documentation. Open `build/docs/html/index.html`
in a browser to view the HTML version.

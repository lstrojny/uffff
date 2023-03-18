{
  description = "ufff";

  inputs = {
    # Use fork until https://github.com/NixOS/nixpkgs/pull/221845 is merged
    nixpkgs.url = "github:lstrojny/nixpkgs/fix-php-wrapper";
    darwin.url = "github:lnl7/nix-darwin/master";
    darwin.inputs.nixpkgs.follows = "nixpkgs";
    flake-utils.url = "github:numtide/flake-utils";
    nix-shell.url = "github:loophp/nix-shell";
  };

  outputs = { self, nixpkgs, darwin, flake-utils, nix-shell }:
    flake-utils.lib.eachDefaultSystem (system:
      let
        pkgs = import nixpkgs { inherit system; };

        php = (nix-shell.api.makePhp pkgs {
          php = "php82";
          withExtensions = [
            "xdebug"
            "mbstring"
            "filter"
            "intl"
            "openssl"
            "dom"
            "tokenizer"
            "xmlwriter"
            "simplexml"
            "opcache"
            "curl"
            "zlib"
            "ctype"
          ];

          extraConfig = ''
            xdebug.mode = coverage
          '';
        });
      in {
        devShells = {
          default = pkgs.mkShellNoCC {
            name = "ufff";

            buildInputs = [
              php
              php.packages.composer
              pkgs.mailhog
              pkgs.sphinx
              (pkgs.python3.withPackages (p: with p; [ sphinx-rtd-theme ]))
            ];
          };
        };
      });
}

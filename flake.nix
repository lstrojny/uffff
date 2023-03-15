{
  description = "ufff";

  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixos-unstable";
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
          withExtensions =
            [ "xdebug" "mbstring" "filter" "intl" "openssl" "dom" "tokenizer" "xmlwriter" "simplexml" "opcache" ];

          extraConfig = ''
            xdebug.mode = coverage
          '';
        });
      in {
        devShells = {
          default = pkgs.mkShellNoCC {
            name = "ufff";

            buildInputs = [ php php.packages.composer pkgs.mailhog ];
          };
        };
      });
}

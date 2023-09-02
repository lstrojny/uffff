{
  description = "Example flake for PHP development";

  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixos-unstable";
    nix-php-composer-builder.url = "github:loophp/nix-php-composer-builder";
    systems.url = "github:nix-systems/default";
  };

  outputs = inputs@{ self, flake-parts, systems, ... }:
    flake-parts.lib.mkFlake { inherit inputs; } {
      systems = import systems;

      perSystem = { config, self', inputs', pkgs, system, lib, ... }:
        let
          php = version:
            pkgs.api.buildPhpFromComposer {
              src = inputs.self;
              php = pkgs."php${version}";
              withExtensions = [
                (pkgs.php83.extensions.xdebug.overrideAttrs (oldAttrs: {
                  src = pkgs.fetchFromGitHub {
                    owner = "xdebug";
                    repo = "xdebug";
                    rev = "7d10e631c17694913eadafb9a07da22fb3736b2c";
                    hash = "sha256-El6kdI1rOTWbwmX8bL1yB9KPwpew8GNK+lOb6h3KiOI=";
                  };
                }))
              ];
            };
        in {
          _module.args.pkgs = import self.inputs.nixpkgs {
            inherit system;
            overlays = [ inputs.nix-php-composer-builder.overlays.default ];
            config.allowUnfree = true;
          };

          devShells.default = pkgs.mkShellNoCC { buildInputs = [ (php "82") (php "82").packages.composer ]; };
          devShells.php83 = pkgs.mkShellNoCC { buildInputs = [ (php "83") (php "83").packages.composer ]; };
        };
    };
}

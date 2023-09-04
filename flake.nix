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
          makePhp = version:
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
          phpVersions = [ "8.2" "8.3" ];
          makeShell = version:
            let phpDrv = makePhp (builtins.replaceStrings [ "." ] [ "" ] version);
            in {
              name = "php${version}";
              value = pkgs.mkShellNoCC { buildInputs = [ phpDrv phpDrv.packages.composer ]; };
            };
          devShells = builtins.listToAttrs (map makeShell phpVersions);
          devShellsWithDefault = devShells // { default = devShells."php${builtins.head phpVersions}"; };
        in {
          _module.args.pkgs = import self.inputs.nixpkgs {
            inherit system;
            overlays = [ inputs.nix-php-composer-builder.overlays.default ];
            config.allowUnfree = true;
          };

          devShells = devShellsWithDefault;
        };
    };
}

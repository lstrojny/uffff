{
  description = "uffff";
  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixos-unstable";
    systems.url = "github:nix-systems/default";
  };

  outputs = inputs@{ self, flake-parts, systems, ... }:
    flake-parts.lib.mkFlake { inherit inputs; } {
      systems = import systems;

      perSystem = { config, self', inputs', pkgs, system, lib, ... }:
        let
          makePhp = version:
            let phpPkg = pkgs."php${builtins.toString version}";
            in phpPkg.buildComposerProject rec {
              name = "lstrojny/uffff";
              pname = name;
              runtimeInputs = [ phpPkg ];
              vendorHash = "sha256-xeurPFckxZjRGUj1bZ6OVQj+VkeHGw29aHOUDG7eOSE=";
              version = "0.2.0";
              src = inputs.self;
              composerNoDev = false;
              php = phpPkg.buildEnv {
                extensions = ({ enabled, all }: enabled ++ (with all; [ xdebug ]));
                extraConfig = "xdebug.mode=coverage";
              };
            };
          phpVersions = [ 83 82 ];
          makeShell = version:
            let phpDrv = makePhp version;
            in {
              name = "php${builtins.toString version}";
              value = pkgs.mkShellNoCC { packages = [ phpDrv phpDrv.php phpDrv.php.packages.composer ]; };
            };
          devShells = builtins.listToAttrs (map makeShell phpVersions);
          devShellsWithDefault = devShells // {
            default = devShells."php${builtins.toString (builtins.head phpVersions)}";
          };
        in { devShells = devShellsWithDefault; };
    };
}

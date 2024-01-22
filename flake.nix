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
            pkgs."php${version}".buildComposerProject {
              name = "lstrojny/uffff";
              pname = "lstrojny/uffff";
              runtimeInputs = [ pkgs."php${version}" ];
              vendorHash = "sha256-9OYFXv6v2amvBKk15PzqTQdIhPc/HSO0UIwn+lQwETs=";
              version = "0.2.0";
              src = inputs.self;
              composerNoDev = false;
              php =
                pkgs."php${version}".buildEnv { extensions = ({ enabled, all }: enabled ++ (with all; [ xdebug ])); };
            };
          phpVersions = [ "8.3" "8.2" ];
          makeShell = version:
            let phpDrv = makePhp (builtins.replaceStrings [ "." ] [ "" ] version);
            in {
              name = "php${version}";
              value = pkgs.mkShellNoCC { buildInputs = [ phpDrv ]; };
            };
          devShells = builtins.listToAttrs (map makeShell phpVersions);
          devShellsWithDefault = devShells // { default = devShells."php${builtins.head phpVersions}"; };
        in { devShells = devShellsWithDefault; };
    };
}

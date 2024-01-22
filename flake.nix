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
              value = pkgs.mkShellNoCC {
                packages = [
                  phpDrv
                  phpDrv.php
                  phpDrv.php.packages.composer
                  pkgs.sphinx
                  (pkgs.python3.withPackages (p: with p; [ pip ]))
                ];
                shellHook = ''
                  # Tells pip to put packages into $PIP_PREFIX instead of the usual locations.
                  # See https://pip.pypa.io/en/stable/user_guide/#environment-variables.
                  export PIP_PREFIX=$(pwd)/build/pip_packages
                  export PYTHONPATH="$PIP_PREFIX/${pkgs.python3.sitePackages}:$PYTHONPATH"
                  export PATH="$PIP_PREFIX/bin:$PATH"
                  unset SOURCE_DATE_EPOCH
                  pip install -r docs/requirements.txt
                '';
              };
            };
          devShells = builtins.listToAttrs (map makeShell phpVersions);
          devShellsWithDefault = devShells // {
            default = devShells."php${builtins.toString (builtins.head phpVersions)}";
          };
        in { devShells = devShellsWithDefault; };
    };
}

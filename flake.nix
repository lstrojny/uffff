{
  description = "ufff";

  inputs = {
    nixpkgs.url = "github:nixos/nixpkgs/nixpkgs-unstable";
    nix-shell.url = "github:loophp/nix-shell";
    nix-shell.inputs.nixpkgs.follows = "nixpkgs";
  };

  outputs = inputs@{ self, flake-parts, ... }:
    flake-parts.lib.mkFlake { inherit inputs; } {
      # This flake is for Linux (x86) and Apple (darwin) systems
      # If you need more systems, inspect `nixpkgs.lib.systems.flakeExposed` and
      # add them to this list.
      #
      # $ nix repl "<nixpkgs>"
      # nix-repl> lib.systems.flakeExposed
      systems = [ "x86_64-linux" "x86_64-darwin" "aarch64-darwin" ];

      perSystem = { pkgs, system, ... }:
        let
          php = inputs.nix-shell.api.makePhp system {
            php = "php82";
            withExtensions = [
              "ctype"
              "curl"
              "dom"
              "filter"
              "intl"
              "mbstring"
              "opcache"
              "openssl"
              "simplexml"
              "tokenizer"
              "xmlwriter"
              "zlib"

              # Development only
              "xdebug"
            ];

            extraConfig = ''
              xdebug.mode = coverage
            '';
          };
        in {
          # Run `nix fmt` to reformat the nix files
          formatter = pkgs.nixfmt;

          devShells.default = pkgs.mkShellNoCC {
            name = "ufff";

            buildInputs = [ php php.packages.composer pkgs.sphinx (pkgs.python3.withPackages (p: with p; [ pip ])) ];

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
    };
}

#!/usr/bin/env bash
echo "Running basic validation..."
find .. -name '*.php' -print0 | xargs -0 -n1 -I{} php -l {}
echo "Done."

#!/usr/bin/env bash
# Render all PlantUML diagrams to SVG
set -euo pipefail
DIR="$(cd "$(dirname "$0")" && pwd)"
JAR="$DIR/plantuml.jar"
java -Djava.awt.headless=true -jar "$JAR" -tsvg "$DIR" -o "$DIR" -charset UTF-8
echo "Done."

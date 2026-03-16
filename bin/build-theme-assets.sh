#!/bin/sh

set -eu

APP_ROOT="${APP_ROOT:-$(pwd)}"
WEBROOT_DIR="${WEBROOT_DIR:-$APP_ROOT/webroot}"
THEME_PACKAGE="${THEME_PACKAGE:-@lbdistrictscouts/district-styles}"
THEME_PACKAGE_VERSION="${THEME_PACKAGE_VERSION:-latest}"
THEME_PACKAGE_DIST_DIR="${THEME_PACKAGE_DIST_DIR:-dist}"
THEME_ASSET_PATH="${THEME_ASSET_PATH:-theme}"
THEME_SOURCE_PATH="${THEME_SOURCE_PATH:-../district-styles}"
THEME_SOURCE_ENTRY="${THEME_SOURCE_ENTRY:-scss/style.scss}"
THEME_OUTPUT_CSS="${THEME_OUTPUT_CSS:-theme.css}"

if [ -z "${NPM_CONFIG_USERCONFIG:-}" ] && [ -f "$APP_ROOT/.npmrc" ]; then
    export NPM_CONFIG_USERCONFIG="$APP_ROOT/.npmrc"
fi

if [ -z "$THEME_PACKAGE" ] && [ -z "$THEME_SOURCE_PATH" ]; then
    echo "Neither THEME_PACKAGE nor THEME_SOURCE_PATH is set; skipping theme asset build."
    exit 0
fi

BUILD_DIR="$(mktemp -d)"
TARGET_DIR="$WEBROOT_DIR/${THEME_ASSET_PATH#/}"
SOURCE_PATH_RESOLVED=""

if [ -n "$THEME_SOURCE_PATH" ]; then
    case "$THEME_SOURCE_PATH" in
        /*) SOURCE_PATH_RESOLVED="$THEME_SOURCE_PATH" ;;
        *) SOURCE_PATH_RESOLVED="$APP_ROOT/$THEME_SOURCE_PATH" ;;
    esac
fi

cleanup() {
    rm -rf "$BUILD_DIR"
}

trap cleanup EXIT INT TERM

cd "$BUILD_DIR"
npm init -y >/dev/null 2>&1

PACKAGE_DIR=""

if [ -n "$SOURCE_PATH_RESOLVED" ] && [ -d "$SOURCE_PATH_RESOLVED" ]; then
    SOURCE_PACKAGE_DIR="$(cd "$SOURCE_PATH_RESOLVED" && pwd)"
    echo "Installing theme package from local source: $SOURCE_PACKAGE_DIR"
    npm install --no-package-lock --no-save "$SOURCE_PACKAGE_DIR"

    if [ -f "$SOURCE_PACKAGE_DIR/package.json" ]; then
        SOURCE_PACKAGE_NAME="$(node -p "require('$SOURCE_PACKAGE_DIR/package.json').name")"
        PACKAGE_DIR="$(node -p "require.resolve('$SOURCE_PACKAGE_NAME/package.json').replace(/\\/package\\.json$/, '')")"
    else
        echo "Theme source package.json not found: $SOURCE_PACKAGE_DIR/package.json" >&2
        exit 1
    fi
else
    if [ -z "$THEME_PACKAGE" ]; then
        echo "THEME_PACKAGE must be set when THEME_SOURCE_PATH is not available." >&2
        exit 1
    fi

    PACKAGE_SPEC="$THEME_PACKAGE"
    if [ -n "$THEME_PACKAGE_VERSION" ] && [ "$THEME_PACKAGE_VERSION" != "latest" ]; then
        PACKAGE_SPEC="$THEME_PACKAGE@$THEME_PACKAGE_VERSION"
    fi

    echo "Installing theme package: $PACKAGE_SPEC"
    npm install --no-package-lock --no-save "$PACKAGE_SPEC"
    PACKAGE_DIR="$(node -p "require.resolve('$THEME_PACKAGE/package.json').replace(/\\/package\\.json$/, '')")"
fi

rm -rf "$TARGET_DIR"
mkdir -p "$TARGET_DIR"

DIST_DIR="$PACKAGE_DIR/$THEME_PACKAGE_DIST_DIR"
if [ -d "$DIST_DIR" ]; then
    cp -R "$DIST_DIR"/. "$TARGET_DIR"/
    echo "Theme assets copied to $TARGET_DIR from dist output"
    exit 0
fi

SOURCE_ENTRY="$PACKAGE_DIR/$THEME_SOURCE_ENTRY"
if [ ! -f "$SOURCE_ENTRY" ]; then
    echo "Theme source entry not found: $SOURCE_ENTRY" >&2
    exit 1
fi

npm install --no-package-lock --no-save sass bootstrap >/dev/null

npx sass \
    --load-path="$BUILD_DIR/node_modules" \
    --load-path="$BUILD_DIR/node_modules/bootstrap/scss" \
    --load-path="$PACKAGE_DIR/node_modules" \
    --load-path="$PACKAGE_DIR/scss" \
    "$SOURCE_ENTRY:$TARGET_DIR/$THEME_OUTPUT_CSS"

echo "Theme stylesheet built to $TARGET_DIR/$THEME_OUTPUT_CSS"

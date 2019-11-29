#!/bin/bash
set -e

echo "Running npm install"
npm install


# `npm run production` bundles assets and minifies javascript / css
# assets using Mix which is a configuration layer on top of Webpack
# See:  https://laravel.com/docs/5.8/mix#running-mix

echo "Running npm run production"
npm run production

# For development purposes, use `npm run dev`

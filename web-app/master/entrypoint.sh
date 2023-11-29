composer install
npm cache clean --force
rm -rf node_modules package-lock.json
npm install
npm start
npm run watch

exec "$@"
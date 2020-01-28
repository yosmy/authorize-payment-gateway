# Test

docker network create backend

cd test

export UID
export GID
docker-compose \
-f docker/all.yml \
-p yosmy_authorize_gateway \
up -d \
--remove-orphans --force-recreate

docker exec -it yosmy_authorize_gateway_php sh
cd test
rm -rf var/cache/*

php bin/app.php /payment/gateway/authorize/add-customer
php bin/app.php /payment/gateway/authorize/add-card 1994876700 4007000000027 04 24 123
php bin/app.php /payment/gateway/authorize/delete-card 1994876700 2013345711
php bin/app.php /payment/gateway/authorize/execute-charge 1994876700 2013352269 1000 Deposito Deposito
php bin/app.php /payment/gateway/authorize/refund-charge 0
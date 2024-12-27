docker build --no-cache . -t gs_rector

# add binds
#--volume=$PWD:/var/www/html \
#--workdir=/var/www/html \
docker run \
--name=gs_rector \
--mount type=bind,source=$PWD,target=/var/www/html \
-p 8080:80 \
--runtime=runc -d gs_rector:latest \

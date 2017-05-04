#!/bin/bash
SCRIPT_DIR="/home/wuzhongyang/src" #这个改成你存放刚刚下载下来的qshell的文件夹位置
BACKUP_SRC="/home/wuzhongyang/www/time/backup" #这个是你想要备份的本地VPS上的文件，不同的目录用空格分开
BACKUP_DST="/home/wuzhongyang/www/time/backup/tmp" #这个是你暂时存放备份压缩文件的地方，一般用/tmp即可
BUCKET="backup" #这个是你七牛空间名称，可以为公开空间或私有空间
MYSQL_SERVER="123.57.41.158" #这个是你mysql服务器的地址，一般填这个本地地址即可
MYSQL_USER="root" #这个是你mysql的用户名名称，比如root或admin之类的
MYSQL_PASS="z20138502@wzy-360" #这个是你mysql用户的密码
MYSQL_DATABASE="time"
# 下面的一般不用改了
NOW=$(date +"%Y.%m.%d")
DESTFILE="$BACKUP_DST/$NOW-$MYSQL_DATABASE.tar.gz"
# 备份mysql数据库并和其它备份文件一起压缩成一个文件
# mysqldump -u $MYSQL_USER -h $MYSQL_SERVER -p$MYSQL_PASS --all-databases > "$NOW-Databases.sql"
mysqldump -u $MYSQL_USER -h $MYSQL_SERVER -p$MYSQL_PASS $MYSQL_DATABASE> "$NOW-$MYSQL_DATABASE.sql"
echo "数据库备份完成，打包网站数据中..."
tar cfzP "$DESTFILE" $BACKUP_SRC "$NOW-$MYSQL_DATABASE.sql"
echo "所有数据打包完成，准备上传..."
# 用脚本上传到七牛云
# $SCRIPT_DIR/qshell rput "$BUCKET" "$NOW.tar.gz"  "$DESTFILE" true
qshell rput "$BUCKET" "$NOW-$MYSQL_DATABASE.tar.gz"  "$DESTFILE" true
if [ $? -eq 0 ];then
     echo "上传完成"
else
     echo "上传失败，重新尝试"
fi
# 删除本地的临时文件
rm -f "$NOW-$MYSQL_DATABASE.sql" "$DESTFILE"

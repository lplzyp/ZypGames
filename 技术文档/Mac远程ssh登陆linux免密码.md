

# mac作为客户端ssh到linux服务器一般的操作都是 ssh -p 端口号 User@Hostname	

# 之后还要输入密码十分麻烦，以下操作将这一过程简化

# 例如 : ssh server


### _操作步骤如下_：

	1,在mac上生成公钥和私钥
		命令 : ssh-keygen -t[rsa|dsa] 之后可一路enter选择默认配置信息
		结果 : 生成 id_rsa.pub(公钥) | id_rsa(私钥) 存放路径 ~/.ssh
	
	2,copy公钥到远程服务器里你分配到的账号下的ssh文件中的authorized_keys文件，比如：分配到的是/home/www下的www账号,可用scp命令copy,需要www对应的密码
		命令 : scp -P 端口号 ~/.ssh/id_rsa.pub www@123.123.123.123:/home/www/.ssh
		结果 : 复制公钥文件id_rsa.pub到/home/www/.ssh
		
	3,将公钥加入到authorized_keys文件中，若不存在，则创建
		命令 : cat -n ./id_rsa.pub >> ./authorized_keys
		结果 : 向authorized_keys文件追加公钥文件的信息
		
	4,这一步至关重要，很多人配置到上一步发现密码还是免不了，其实就是因为.ssh和authorized_keys这两文件的权限不对，ssh免密需要严格限制.ssh为700,authorized_keys为600
		命令 : chmod 700 /home/www/.ssh
			   chmod 600 /home/www/.ssh/authoried_keys
	
	到了这里已经可以 ssh -p 端口号 www@123.123.123.123 免密码登陆

	5，配置config文件可简化登陆信息
		在~/.ssh文件下打开config，若没有，则新建
			Host server # 别名
				Hostname 123.123.123.123 # 主机或域名
				Port 22 # 默认为22，若为其它的话要配置此项
				User www # ssh账号
				IdentityFile ~/.ssh/is_rsa #私钥位置
				
		配置完配置信息保存后保存
		现在就可以直接 ssh server登陆到远程服务器
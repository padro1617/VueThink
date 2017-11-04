
select FROM_UNIXTIME(u.create_time,'%Y-%m-%d %H:%i:%s') as create_time,
u.realname,u.phone,u.bankcard,
ut.realname as trealname,ut.tcode

from oa_admin_user as u 
left join oa_admin_user as ut on ut.id=u.tuid

where u.status=1 and u.id>10 and u.tuid>-1


select pl.postid,p.name,pl.user_id from oa_admin_postlog as pl 
left join oa_admin_post as p
on pl.postid=p.id



CREATE DEFINER=`root`@`localhost` PROCEDURE `gettj_userpostbypage`(`_pageindex` int(11),`_pagelimit` int(11))
BEGIN
     DECLARE t_calstr1 VARCHAR(800);
     DECLARE t_calstr2 VARCHAR(800);
     DECLARE t_sqlstr VARCHAR(800);
     DECLARE t_pagestr VARCHAR(100); --  分页的代码
     DECLARE t_postlogstr VARCHAR(800);
     -- DECLARE t_postcount int default 0;
     -- DECLARE t_i int default 0; -- 变量声明
     DECLARE t_postid int default 0; -- 上一个post的id
     DECLARE t_postname VARCHAR(100); -- 上一个post的name
     -- select count(id) into t_postcount from oa_admin_post;
     
      -- 遍历数据结束标志
      DECLARE done INT DEFAULT FALSE;
      -- 游标
      DECLARE cur CURSOR FOR 
              SELECT id,name FROM oa_admin_post;
      -- 将结束标志绑定到游标
      DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
      -- 打开游标
      OPEN cur;
      
      -- 开始循环
      read_loop: LOOP
        -- 提取游标里的数据，这里只有一个，多个的话也一样；
        FETCH cur INTO t_postid,t_postname;
        -- 声明结束的时候
        IF done THEN
          LEAVE read_loop;
        END IF;
        -- 这里做你想做的循环的事件        
           SET t_postlogstr=CONCAT_WS(' ',t_postlogstr,",(select count(id) from oa_admin_postlog as p where p.user_id=u.id and p.postid=",t_postid,") as '",t_postname,"'");

      END LOOP;
      -- 关闭游标
      CLOSE cur;

     SET _pageindex=IFNULL(_pageindex,1);
     SET _pagelimit=IFNULL(_pagelimit,15);
     SET t_calstr1=" select u.id as '用户ID',FROM_UNIXTIME(u.create_time,'%Y-%m-%d %H:%i:%s') as '注册时间',u.realname as '姓名',u.bankcard as '银行卡号',ut.realname as '推广员姓名',ut.tcode as '推广员专属ID'";
     if _pageindex>-1 then 
          -- 需要分页
          -- SET t_calstr1=" select u.id,FROM_UNIXTIME(u.create_time,'%Y-%m-%d %H:%i:%s') as create_time,u.realname,u.phone,u.bankcard,ut.realname as trealname,ut.tcode ";
          SET t_pagestr=CONCAT_WS(' ',' LIMIT',(_pageindex-1)*_pagelimit,',',_pagelimit);
     else 
          -- 不需要分页 导出的数据
          SET t_calstr1=CONCAT_WS(' ',t_calstr1," ,u.phone as '注册手机',u.idcard as '身份证号',u.bankphone as '银行预留手机号'");
          SET t_pagestr=CONCAT_WS(' ',' LIMIT',_pagelimit);
     end if;
     SET t_calstr2=" from oa_admin_user as u left join oa_admin_user as ut on ut.id=u.tuid where u.status=1 and u.id>10 and u.tuid>-1 ORDER BY u.id ";
     SET t_sqlstr = CONCAT_WS(' ',t_calstr1,t_postlogstr,t_calstr2,t_pagestr);
     -- 打印
     -- select t_sqlstr;
     
     SET @v_sql=t_sqlstr;   --  注意很重要，将连成成的字符串赋值给一个变量（可以之前没有定义，但要以@开头）
     prepare stmt from @v_sql;  --  预处理需要执行的动态SQL，其中stmt是一个变量
     EXECUTE stmt;      --  执行SQL语句
     deallocate prepare stmt;     --  释放掉预处理段
     
END;

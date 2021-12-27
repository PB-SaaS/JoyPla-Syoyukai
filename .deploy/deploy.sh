#!/bin/bash
#
#
#java -jar custom_module.jar -t $TOKEN -s $SECRET $*

TARGET_DIR='../src'
TARGET='NewJoyPla'
DATE=`date '+%y%m%d%H%M%S'`
LOG_OUT="logs/deploy.log"
ZIP_FILE="upload_file"


TOKEN=00011KB9HzJA6571fc2a62048af337abb32cbab1e0dfa3c8aadb
SECRET=f2bf4a8e7b3567fdba896429ea1c136e89320175

# JoyPla_test
#TOKEN=00011BfE-mBBaaf742aa7b0ea74e8aeaa28d065d8415e5f58818
#SECRET=006aca377aa20e97670d4951435c3e162e072a3b

# JoyPla_MCHdemo2
#TOKEN=00002G4B6HfFdf61bd46804474f793ccd0f663ab01cdce5951ab
#SECRET=f340f234d86f2a7a3c31d69c829531ce86d6f0ab

# JoyPla_staging
#TOKEN=00011B9A9Hjf0ef33b8cc05ac2d791c36eb37bf88040bb05417a
#SECRET=cdd9b6ab7cade2db2f038992fa4f18ad5a457dd2

#JoyPla_Stery2
#TOKEN=00051ch0wBaH42eef83b68d011985615a2ed3886c947f42d57e2
#SECRET=94d8c9199dd27752c5ac9cd67ea5aceb124b9313

log() {
    echo "[$(date +"%Y-%m-%d %H:%M:%S")][INFO] $@" | tee -a ${LOG_OUT}
}

errorlog() {
    echo "[$(date +"%Y-%m-%d %H:%M:%S")][ERROR] $@" | tee -a ${LOG_OUT}
    exit
}

if [ -e logs ] ;then
    log "directory logs"
else 
    if mkdir logs ;then
        log "make directory logs"
    else
        errorlog "make directory logs"
    fi
fi

if [ -e tmp ] ;then
    log "directory tmp"
else 
    if mkdir tmp ;then
        log "make directory tmp"
    else
        errorlog "make directory tmp"
    fi
fi

if [ -e tmp/${TARGET} ] ;then
    if rm -rf tmp/${TARGET} ;then
        log "rm directory ${TARGET}"
    else
        errorlog "rm directory ${TARGET}"
    fi
fi

if [ -e tmp/${ZIP_FILE}.zip ] ;then
    if rm -rf tmp/${ZIP_FILE}.zip ;then
        log "rm zip file"
    else
        errorlog "rm zip file"
    fi
fi

if [ -e tmp/${ZIP_FILE}.zip ] ;then
    if rm -rf tmp/${ZIP_FILE}.zip ;then
        log "rm zip file"
    else
        errorlog "rm zip file"
    fi
fi

if cp -r ${TARGET_DIR}/${TARGET} tmp ;then
    log "cp directory ${TARGET}"
else
    errorlog "cp directory ${TARGET}"
fi

cd tmp
if zip -r ${ZIP_FILE} ${TARGET} ;then
    cd -
    log "zip directory ${TARGET}"
else
    cd -
    errorlog "zip directory ${TARGET}"
fi

if rm -rf tmp/${TARGET} ;then
    log "rm directory ${TARGET}"
else
    errorlog "rm directory ${TARGET}"
fi

cd tmp
if php ../upload.php ${TOKEN} ${SECRET} ${ZIP_FILE}.zip ;then
    cd -
    log "upload ${TARGET}"
else 
    cd -
    errorlog "upload ${TARGET}"
fi

#if java -jar custom_module.jar -t $TOKEN -s $SECRET tmp/upload_data.zip ;then
#    log "upload ${TARGET}"
#else 
#    errorlog "upload ${TARGET}"
#fi

exit;
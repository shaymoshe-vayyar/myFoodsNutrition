import mysql
import mysql.connector
from typing import Literal


### https://www.pythontutorial.net/python-basics/python-write-text-file/
### Link to MySql DB Management: http://localhost/phpmyadmin/index.php?route=/database/structure&server=1&db=ajax_demo&table=user

__host__ = 'pc'  # Default


# Internal
def openConn():
    if (__host__ == 'pc'):
        __hostname__ = 'localhost'
        __username__ = 'root'
        __password__ = ''
        database = 'ajax_demo'
        backupDB = 'ajax_demo_backup'
    else:
        __hostname__ = '193.203.166.13'  # '127.0.0.1';
        __username__ = 'u230048523_shay'
        __password__ = 'MosheMoshe1!'
        database = "u230048523_ajax_demo"
        backupDB = 'ajax_demo_backup'

    mySqlConn = mysql.connector.connect(
        host=__hostname__,
        user=__username__,
        password=__password__,
        database=database
    )

    mycursor = mySqlConn.cursor()
    return database, mySqlConn,mycursor, backupDB

__database__,__mySqlConn__,__mycursor__,__backupDB__ = openConn()

# Both Internal and External
def closeConn():
    __mySqlConn__.close()

# External
def setHost(cur_host : Literal['pc','web'], hostName : Literal['pc','web']):
    __host__ = cur_host
    if (cur_host != hostName):
        closeConn()
        __host__ = hostName
        openConn()
    return __host__

# Connection is stayed open as long the python is alive and closed upon Python ends
## 2 types of SQL tables:
#   * key-value sql. the index is usually the key, and the return value is the value. column names are 'key','value'
#           Stored as dictionary
#   * 2D table of multiples values per key/index, can be with primary key or with index, index can be unique or not, can be auto-increment
#           columns names are table's specific, default columns values as input,
#           columns types are derived automatically from cell-values' types
#           stored as array of array in Python
# Operations are:
#   * Load all table
#   * Save all table
#   * Creat an empty table if not exists
#   * Check if table exists
#   * Add Item
#   * Update Item
#   * Delete Table (including backup option)

def CreateTable(tableName,
                colsNamesAndTypes: dict,
                PrimaryKeyName=None,
                colProp: dict[str,Literal['None','Index','UniqueIndex','AutoIncreament']] = None,
                colDefValues: dict = None,
                ifExists: Literal['fail', 'replace'] = 'fail'
                ):
    strColWithTypes = ''
    strCols = ''
    colsName = list(colsNamesAndTypes.keys())
    strDefValue = ''
    for colName in colsName:
        match colsNamesAndTypes[colName]:
            case 'int64' | 'int':
                typeName = 'INT'
            case 'float64':
                typeName = 'DOUBLE'
            case 'float' | 'float32':
                typeName = 'FLOAT' # Assuming float32 is enough
            case 'str':
                typeName = 'VARCHAR(255)'
            case 'object':  # Assuming object is string
                typeName = 'VARCHAR(255)'
            case other:
                print(colTypes[colName])
                raise Exception('type not defined!')
        if colDefValues is not None:
            if colName in colDefValues:
                defValue = colDefValues[colName]
                if (isinstance(defValue,str)):
                    defValue = f"'{defValue}'"
                elif defValue is None:
                    strDefValue = ''
                else:
                    defValue = str(defValue)
                strDefValue = f" DEFAULT {defValue}"
        if (len(strColWithTypes) > 0):
            strColWithTypes = strColWithTypes + ", " + colName + " " + typeName + strDefValue
            strCols = strCols + ", " + colName
        else:
            strColWithTypes = colName + " " + typeName + strDefValue
            strCols = colName
    # Check if we need to override/append existing table
    if (ifExists == 'fail'):
        if checkIfTableExists(tableName):
            return False
    if (ifExists == 'replace'): # Table should be deleted and replaced
        deleteTable(tableName)

    __mycursor__.execute("CREATE TABLE IF NOT EXISTS {table} ({strColWithTypes})".format(table=tableName,
                                                                                     strColWithTypes=strColWithTypes))
    if (PrimaryKeyName is not None):
        # check already exists
        __mycursor__.execute("SELECT * FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE CONSTRAINT_TYPE = 'PRIMARY KEY' AND TABLE_NAME = '{table}';".format(table=tableName))
        myresult = __mycursor__.fetchall()
        if (myresult is None) or (len(myresult)==0):
            __mycursor__.execute("ALTER TABLE `{table}` ADD PRIMARY KEY(`{PrimaryKeyName}`);".format(table=tableName,PrimaryKeyName=PrimaryKeyName))
        else:
            raise Exception("Primary Key already exists")

    __mySqlConn__.commit()
    return True

def deleteTable(tableName : str,
                flagToDoBackup : bool = False):
    if (flagToDoBackup):
        import datetime
        newTableName = tableName+'_'+datetime.datetime.now().strftime("%Y%m%d_%H%M%S")
        __mycursor__.execute(f"CREATE TABLE {__backupDB__}.{newTableName} SELECT * FROM {__database__}.{tableName};")

    __mycursor__.execute(f"DROP TABLE IF EXISTS {tableName};")
    __mySqlConn__.commit()

def checkIfTableExists(tableName : str):
    __mycursor__.execute(f"SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema = '{__database__}' AND table_name = '{tableName}');")
    isExists = __mycursor__.fetchall()
    return (isExists[0][0]==1)

def addItem(tableName : str,
            colsName : list[str],
            values : list):
    strCols = ','.join(colsName)
    strValues = str(values).replace('[','').replace(']','')
    __mycursor__.execute(f"INSERT INTO {tableName} ({strCols}) VALUES ({strValues});")
    __mySqlConn__.commit()

def getItem(tableName : str,
            colNameToSearch : str,
            colValuesToSearch : str,
            colsNameToGet : list[str] = None # None to get all
            ):
    if (colsNameToGet is None):
        __mycursor__.execute(f"SELECT * FROM `{tableName}` WHERE {colNameToSearch}='{colValuesToSearch}';")
    else:
        strColsNameToGet = ','.join(colsNameToGet)
        __mycursor__.execute(f"SELECT {strColsNameToGet} FROM `{tableName}` WHERE {colNameToSearch}='{colValuesToSearch}';")
    values = __mycursor__.fetchall()
    return values

def updateItem(tableName : str,
            colNameToSearch : str,
            colValueToSearch : str,
            colsValuesToUpdate : list,
            colsNamesToUpdate : list[str] = None, # None to update all
            isCommit = True
            ):
    if (colsNamesToUpdate is None):
        strColsNameToUpdate = '*'
    else:
        strColsNameToUpdate = ','.join(colsNamesToUpdate)
    strColsAndValues = ''
    for ii in range(len(colsNamesToUpdate)):
        if (len(strColsAndValues)==0):
            colName = colsNamesToUpdate[ii]
            val = colsValuesToUpdate[ii]
            if (isinstance(val,str)):
                val = f"'{val}'"
            strColsAndValues += f"{colName}={val}"
        else:
            strColsAndValues += f",{colName}={val}"
    __mycursor__.execute(f"UPDATE {tableName} SET {strColsAndValues} WHERE {colNameToSearch}='{colValueToSearch}'")
    if (isCommit):
        __mySqlConn__.commit()

def loadAllRows(tableName : str,
            colsNameToGet : list[str] = None # None to get all
            ):
    if (colsNameToGet is None):
        __mycursor__.execute(f"SELECT * FROM `{tableName}`;")
    else:
        strColsNameToGet = ','.join(colsNameToGet)
        __mycursor__.execute(f"SELECT {strColsNameToGet} FROM `{tableName}`;")
    values = __mycursor__.fetchall()
    return values


import mysql
import mysql.connector
from typing import Literal, List


### https://www.pythontutorial.net/python-basics/python-write-text-file/
### Link to MySql DB Management: http://localhost/phpmyadmin/index.php?route=/database/structure&server=1&db=ajax_demo&table=user

class SqlConnection(object):
    _sql_host_ = ['pc']
    _sql_db_ = None
    _sql_connection_ = {'pc':None,'web':None}
    _sql_cursor_ = {'pc':None,'web':None}

    # parameterized constructor
    def __init__(self, sql_host : List[Literal['pc','web']],
                 sql_db : str = None):
        self._sql_host_ = sql_host
        self._sql_db_ = sql_db
        self.__open_connection_if_none__()

    def __del__(self):
        self.__close_connection_if_open__()

    def __open_connection_if_none__(self):
        for host in self._sql_host_:
            if (self._sql_connection_[host] is None) or (not self._sql_connection_[host].is_connected()):
                if host == 'web':
                    # Note!!! The remote SQL access from the user IP should be enabled from the site
                    hostname = 'mydailynutrition.site' # '193.203.166.13'  # '127.0.0.1';
                    username = 'u230048523_shay2'
                    password = 'MosheMoshe1!'
                    database = 'u230048523_nutrition' #"u230048523_ajax_demo"
                else:
                    hostname = 'localhost'
                    username = 'root'
                    password = ''
                    database = 'nutrition_app' #'ajax_demo'

                if (self._sql_db_ is not None):
                    database = self._sql_db_

                self._sql_connection_[host] = mysql.connector.connect(
                    host=hostname,
                    user=username,
                    password=password,
                    database=database
                )

                self._sql_cursor_[host] = self._sql_connection_[host].cursor()

    def __close_connection_if_open__(self):
        for host in self._sql_host_:
            try:
                if (self._sql_connection_[host] is not None) and self._sql_connection_[host].is_connected():
                    # self._sql_cursor_[host].close()
                    self._sql_connection_[host].close()
            finally:
                self._sql_connection_[host] = None

    def execute(self, sql_query : str):
        self.__open_connection_if_none__()
        for host in self._sql_host_:
            self._sql_cursor_[host].execute(sql_query)

    def fetchall(self):
#        self.__open_connection_if_none__()
        prev_result = None
        for host in self._sql_host_:
            result = self._sql_cursor_[host].fetchall()
            if (prev_result == None):
                prev_result = result
            else:
                assert(prev_result == result)
        self.__close_connection_if_open__()
        return result

    def commit(self):
        self.__open_connection_if_none__()
        for host in self._sql_host_:
            self._sql_connection_[host].commit()

        self.__close_connection_if_open__()

    def close(self):
        self.__close_connection_if_open__()

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

class DatabaseHandler(object):
    _sql_connection_ = None # SqlConnection(['pc','web'])
    # def __new__ (cls):
    #     if not hasattr(cls, 'instance'):
    #         cls.instance = super(DatabaseHandler, cls).__new__ (cls)
    #     return cls.instance

    # parameterized constructor
    def __init__(self, sql_host : List[Literal['pc','web']],
                 sql_db : str = None):
        self._sql_host_ = sql_host
        self._sql_db_ = sql_db
        self.set_host(sql_host, sql_db)

    def set_host(self,host_list : List[Literal['pc','web']],
                 sql_db : str = None):
        self._sql_connection_ = SqlConnection(host_list, sql_db)

    def checkIfTableExists(self, table_name : str):
        self._sql_connection_.execute(f"SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = '{table_name}');")
        isExists = self._sql_connection_.fetchall()
        return (isExists[0][0]==1)

    def __prepare_columns_name_for_sql_request(self,
                                               colsNamesAndTypes: dict,
                                               colDefValues: dict = None):
        colsName = list(colsNamesAndTypes.keys())
        strColWithTypes = ''
        strCols = ''
        strDefValue = ''
        for colName in colsName:
            match colsNamesAndTypes[colName]:
                case 'int64' | 'int' | 'numpy.int64':
                    typeName = 'INT'
                case 'float64' | 'numpy.float64':
                    typeName = 'DOUBLE'
                case 'float' | 'float32':
                    typeName = 'FLOAT'  # Assuming float32 is enough
                case 'str':
                    typeName = 'VARCHAR(255)'
                case 'object':  # Assuming object is string
                    typeName = 'VARCHAR(255)'
                case 'date':
                    typeName = 'date'
                case 'time':
                    typeName = 'time'
                case other:
                    print(colsNamesAndTypes[colName])
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
        return strColWithTypes

    def CreateTable(self,
                    table_name,
                    colsNamesAndTypes: dict,
                    PrimaryKeyName=None,
                    isPrimaryAutoIncrement = True,
                    colProp: dict[str,Literal['Index','UniqueIndex']] = None,
                    colDefValues: dict = None,
                    ifExists: Literal['fail', 'replace'] = 'fail'
                    ):
        strColWithTypes = self.__prepare_columns_name_for_sql_request(colsNamesAndTypes, colDefValues)
        # Check if we need to override/append existing table
        if (ifExists == 'fail'):
            if self.checkIfTableExists(table_name):
                return False
        if (ifExists == 'replace'): # Table should be deleted and replaced
            self.deleteTable(table_name)

        self._sql_connection_.execute("CREATE TABLE IF NOT EXISTS {table} ({strColWithTypes})".format(table=table_name,
                                                                                         strColWithTypes=strColWithTypes))
        if (PrimaryKeyName is not None):
            # check already exists
            self._sql_connection_.execute("SELECT * FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE CONSTRAINT_TYPE = 'PRIMARY KEY' AND TABLE_NAME = '{table}';".format(table=table_name))
            myresult = self._sql_connection_.fetchall()
            if (myresult is None) or (len(myresult)==0):
                if (isPrimaryAutoIncrement):
                    self._sql_connection_.execute(f"ALTER TABLE `{table_name}` MODIFY COLUMN `{PrimaryKeyName}` INT AUTO_INCREMENT PRIMARY KEY;")
                else:
                    self._sql_connection_.execute(f"ALTER TABLE `{table_name}` ADD PRIMARY KEY(`{PrimaryKeyName}`);")
            else:
                raise Exception("Primary Key already exists")

        if (colProp is not None):
            for colName,colPropV in colProp.items():
                if (colPropV == 'UniqueIndex'):
                    self._sql_connection_.execute(f"ALTER TABLE `{table_name}` ADD UNIQUE (`{colName}`);")
                elif colPropV == 'Index':
                    self._sql_connection_.execute(f"ALTER TABLE `{table_name}` ADD INDEX (`{colName}`);")
                else:
                    raise Exception("Unknown column property!")

        self._sql_connection_.commit()
        return True

    def deleteTable(self,
                    table_name : str,
                    flagToDoBackup : bool = False):
        if (flagToDoBackup):
            raise Exception("Not supported yet")
            # import datetime
            # newTableName = tableName+'_'+datetime.datetime.now().strftime("%Y%m%d_%H%M%S")
            # __mycursor__.execute(f"CREATE TABLE {__backupDB__}.{newTableName} SELECT * FROM {__database__}.{tableName};")

        self._sql_connection_.execute(f"DROP TABLE IF EXISTS {table_name};")
        self._sql_connection_.commit()
    def addItem(self,table_name : str,
                cols_name : list[str],
                values : list):
        strCols = ','.join(cols_name)
        strValues = str(values).replace('[','').replace(']','')
        self._sql_connection_.execute(f"INSERT INTO {table_name} ({strCols}) VALUES ({strValues});")
        self._sql_connection_.commit()

    def getItem(self,
                table_name : str,
                colNameToSearch : str,
                colValuesToSearch : str,
                colsNameToGet : list[str] = None # None to get all
                ):
        if (colsNameToGet is None):
            self._sql_connection_.execute(f"SELECT * FROM `{table_name}` WHERE {colNameToSearch}='{colValuesToSearch}';")
        else:
            strColsNameToGet = ','.join(colsNameToGet)
            self._sql_connection_.execute(f"SELECT {strColsNameToGet} FROM `{table_name}` WHERE {colNameToSearch}='{colValuesToSearch}';")
        values = self._sql_connection_.fetchall()
        return values

    def searchItem(self,
                table_name : str,
                colNameToSearch : str,
                colValuesToSearch : str,
                colsNameToGet : list[str] = None, # None to get all
                search_mode : Literal['like','regexp','equal'] = 'equal'
                ):
        str_srch = '==' # default
        match search_mode:
            case 'like':
                str_srch = 'LIKE'
            case 'regexp':
                str_srch = 'REGEXP'

        if (colsNameToGet is None):
            strColsNameToGet = '*'
            self._sql_connection_.execute(f"DESCRIBE {table_name};")
            columns_info = self._sql_connection_.fetchall()
            columns_names = [column_info[0] for column_info in columns_info]
        else:
            strColsNameToGet = ','.join(colsNameToGet)

        self._sql_connection_.execute(
            f"SELECT {strColsNameToGet} FROM `{table_name}` WHERE {colNameToSearch} {str_srch} '{colValuesToSearch}';")
        values = self._sql_connection_.fetchall()
        if colsNameToGet is None:
            table_dict_list = []
            for value in values:
                table_dict_list.append({columns_names[ii] : value[ii] for ii in range(len(columns_names))})
            return table_dict_list
        else:
            return values

    def updateItem(self,
                   tableName : str,
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
        self._sql_connection_.execute(f"UPDATE {tableName} SET {strColsAndValues} WHERE {colNameToSearch}='{colValueToSearch}'")
        if (isCommit):
            self._sql_connection_.commit()

    def loadAllRows(self,
                    tableName : str,
                colsNameToGet : list[str] = None # None to get all
                ):
        if (colsNameToGet is None):
            self._sql_connection_.execute(f"SELECT * FROM `{tableName}`;")
        else:
            strColsNameToGet = ','.join(colsNameToGet)
            self._sql_connection_.execute(f"SELECT {strColsNameToGet} FROM `{tableName}`;")
        values = self._sql_connection_.fetchall()
        return values

    def get_columns_names(self, table_name : str):
        # self._sql_connection_.execute(
        #     f"SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = {table_name};")
        self._sql_connection_.execute(
            f"SHOW COLUMNS FROM `{table_name}`;")

        columns_info = self._sql_connection_.fetchall()
        columns_names = [column_info[0] for column_info in columns_info]
        return columns_names

    def add_column(self, table_name: str, colsNamesAndTypes: dict, colDefValues: dict = None):
        strColWithTypes = self.__prepare_columns_name_for_sql_request(colsNamesAndTypes, colDefValues)
        add_columns_arr_string = ', '.join(['ADD COLUMN '+strColWithType for strColWithType in strColWithTypes.split(',')])
        # print(add_columns_arr_string)
        self._sql_connection_.execute(f"ALTER TABLE `{table_name}` {add_columns_arr_string};")
        self._sql_connection_.commit()


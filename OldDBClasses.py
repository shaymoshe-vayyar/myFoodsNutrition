import pandas as pd
from typing import Literal
from math import isnan

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
#   * Update Item
#   * Delete Table (including backup option)
__DBItemsSourceLinksTableName__ = 'tableItemsSourceLinks'

def ReadTableAsDF(mydbConn,tableName, index_col=None):
    frame = None
    try:
        mycursor = mydbConn.cursor()
        mycursor.execute(f"SELECT * FROM {table}".format(table=tableName))
        myresult = mycursor.fetchall()
        # for x in myresult:
        #     print(x)
        frame = pd.DataFrame(myresult, columns=mycursor.column_names, index_col=index_col)
        # frame = pd.read_sql('SELECT * FROM {table}'.format(table=tableName), self.mydbConn, index_col=index_col);
    except ValueError as vx:
        print(vx)
    except Exception as ex:
        print(ex)
    return frame


def CreateTableFromDF(mydbConn,
                      dataFrame: pd.DataFrame,
                      tableName,
                      PrimaryKeyName=None,
                      ifExists: Literal['fail', 'replace', 'append'] = 'fail'):
    # dataFrame.to_sql(tableName, self.mydbConn, if_exists=ifExists);
    mycursor = mydbConn.cursor()
    strColWithTypes = ''
    strCols = ''
    for ii in range(dataFrame.columns.size):
        colName = dataFrame.columns[ii]
        pyType = dataFrame[colName].dtype
        match pyType.name:
            case 'int64':
                typeName = 'INT'
            case 'float64':
                typeName = 'FLOAT' # Assuming float32 is enough
            case 'object':  # Assuming object is string
                typeName = 'VARCHAR(255)'
            case other:
                print(pyType.name)
                raise Exception('type not defined!')
        if (ii > 0):
            strColWithTypes = strColWithTypes + ", " + colName + " " + typeName
            strCols = strCols + ", " + colName
        else:
            strColWithTypes = colName + " " + typeName
            strCols = colName
    mycursor.execute("CREATE TABLE IF NOT EXISTS {table} ({strColWithTypes})".format(table=tableName,
                                                                                     strColWithTypes=strColWithTypes))
    if (PrimaryKeyName is not None):
        # check already exists
        mycursor.execute("SELECT * FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE CONSTRAINT_TYPE = 'PRIMARY KEY' AND TABLE_NAME = '{table}';".format(table=tableName))
        myresult = mycursor.fetchall()
        if (myresult is None) or (len(myresult)==0):
            mycursor.execute("ALTER TABLE `{table}` ADD PRIMARY KEY(`{PrimaryKeyName}`);".format(table=tableName,PrimaryKeyName=PrimaryKeyName))
    for valueVec in dataFrame.values:
        strValues = ''
        for value in valueVec:
            # Check if value exists (not exist value are marked with 'nan' in pandas)
            if type(value) == int or type(value) == float:
                if isnan(value):
                    value = 0.0
            if (len(strValues) == 0):  # first
                sepElem = " "
            else:
                sepElem = ", "

            if type(value).__name__ == 'str':
                strValues = strValues + sepElem + "'" + value + "'"
            else:
                strValues = strValues + sepElem + str(value)
        mycursor.execute(
            "INSERT INTO {table} ({strCols}) VALUES ({strValues});".format(table=tableName, strCols=strCols,
                                                                           strValues=strValues))

    mydbConn.commit()
    print("Done")

def CreateTableFromKeyValueDict(mydbConn,
                      KeyValueDict: dict,
                      tableName,
                      KeyColName='keyCol',
                      ValueColName='valueCol',
                      ifExists: Literal['fail', 'replace', 'append'] = 'fail'):
    # dataFrame.to_sql(tableName, self.mydbConn, if_exists=ifExists);
    mycursor = mydbConn.cursor()
    if (ifExists=='fail'):
        mycursor.execute("CREATE TABLE {table} ({KeyColName} varchar(255),{ValueColName} varchar(255));".format(table=tableName,
                                                                                                                             KeyColName = KeyColName,
                                                                                                                             ValueColName = ValueColName))
    else: # replace or append -> Create the table only if not exists
        mycursor.execute(
            "CREATE TABLE IF NOT EXISTS {table} ({KeyColName} varchar(255),{ValueColName} varchar(255));".format(
                table=tableName,
                KeyColName=KeyColName,
                ValueColName=ValueColName))

    if (ifExists == 'replace'):
        mycursor.execute(f"TRUNCATE `{tableName}`")

    for keyI in KeyValueDict:
        valueI = KeyValueDict[keyI]
        mycursor.execute(f"INSERT INTO {tableName} ({KeyColName}, {ValueColName}) VALUES ('{keyI}', '{valueI}');")

    mydbConn.commit()

class DBItemsNutClass:
    __itemName__ = 'itemName'
    __tableItemNutValues__ = None
    __tableName__ = 'db_items_nut'

    def AddNutsListPerItem(self,itemName, tableNut):
        tableNut[self.__itemName__] =   itemName
        if self.__tableItemNutValues__ is None:
            newItemIdx = 0
            self.__tableItemNutValues__ = pd.DataFrame(tableNut,index=[newItemIdx])
        else:
            newItemIdx = len(self.__tableItemNutValues__.index)
            self.__tableItemNutValues__.loc[newItemIdx]=tableNut

    def LoadFromDB(self, dbConn):
        self.__tableItemNutValues__ = ReadTableAsDF(dbConn, self.__tableName__)

    def SaveToDB(self, dbConn):
        CreateTableFromDF(dbConn, self.__tableItemNutValues__,
                            self.__tableName__,
                            PrimaryKeyName=self.__itemName__,
                            ifExists = 'replace')

    def AddItemToSqlDb(item : dict):
        strCols =  ','.join(list(item.keys()))
        strValues =  str(list(item.values())).replace('[','').replace(']','')
        from databaseHandler import getSqlConnection
        mycursor = getSqlConnection().mydbConn.cursor()
        mycursor.execute("INSERT INTO {table} ({strCols}) VALUES ({strValues});".format(table=DBItemsNutClass.__tableName__,
                                                                                        strCols=strCols,
                                                                       strValues=strValues))
        getSqlConnection().mydbConn.commit()


def Read2DTableFromSql(tableName : str):
    strCols = ','.join(list(item.keys()))
    strValues = str(list(item.values())).replace('[', '').replace(']', '')
    from databaseHandler import getSqlConnection
    mycursor = getSqlConnection().mydbConn.cursor()
    mycursor.execute("INSERT INTO {table} ({strCols}) VALUES ({strValues});".format(table=DBItemsNutClass.__tableName__,
                                                                                    strCols=strCols,
                                                                                    strValues=strValues))
    getSqlConnection().mydbConn.commit()


# def deleteSqlTable(tableName : str)

def Save2DTableToSql(tableName : str,
                     arr_Items_Fields : list,
                    primaryKeyName : str = None,
                    ifExists : Literal['fail', 'replace', 'append'] = 'replace'):
    strcols = ','.join(list(itemdict_kv.keys()))
    strvalues = str(list(itemdict_kv.values())).replace('[', '').replace(']', '')
    from databaseHandler import getSqlConnection
    mycursor = getSqlConnection().mydbConn.cursor()
    mycursor.execute("INSERT INTO {table} ({strCols}) VALUES ({strValues});".format(table=tablename,
                                                                                    strCols=strcols,
                                                                                    strValues=strvalues))
    getSqlConnection().mydbConn.commit()

def AddItemTo2DSqlTable(tablename : str,
                        itemdict_kv : dict):
    strcols = ','.join(list(itemdict_kv.keys()))
    strvalues = str(list(itemdict_kv.values())).replace('[', '').replace(']', '')
    from databaseHandler import getSqlConnection
    mycursor = getSqlConnection().mydbConn.cursor()
    mycursor.execute("INSERT INTO {table} ({strCols}) VALUES ({strValues});".format(table=tablename,
                                                                                    strCols=strcols,
                                                                                    strValues=strvalues))
    getSqlConnection().mydbConn.commit()


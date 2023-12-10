from database_handler import  SqlConnection, DatabaseHandler
import unittest

connections_to_check = ['pc'] # ['web','pc']

class Testing(unittest.TestCase):
    def test_sql_connection1(self):
        sql_conn = SqlConnection(['pc'])
        tableName = 'user'
        sql_conn.execute(
            f"SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = '{tableName}');")
        isExists = sql_conn.fetchall()
        self.assertTrue(isExists[0][0] == 1)
        tableName = 'blabla'
        sql_conn.execute(
            f"SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = '{tableName}');")
        isExists = sql_conn.fetchall()
        self.assertTrue(isExists[0][0] == 0)

    # def test_sql_connection2(self):
    #
    #     sql_conn = SqlConnection(['pc','web'])
    #     tableName = 'user'
    #     sql_conn.execute(
    #         f"SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = '{tableName}');")
    #     isExists = sql_conn.fetchall()
    #     self.assertTrue(isExists[0][0] == 1)
    #     tableName = 'blabla'
    #     sql_conn.execute(
    #         f"SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = '{tableName}');")
    #     isExists = sql_conn.fetchall()
    #     self.assertTrue(isExists[0][0] == 0)

    def test_table_exists(self):
        dbh = DatabaseHandler(connections_to_check)
        res = dbh.checkIfTableExists('blabla')
        self.assertTrue(res == False)

        res = dbh.checkIfTableExists('user')
        self.assertTrue(res == True)

    def test_table_create_and_delete(self):
        dbh = DatabaseHandler(connections_to_check)

        # Check simple key-value

        dbh.deleteTable('my_test_table123')

        res = dbh.CreateTable('my_test_table123',
                                    colsNamesAndTypes={'keyCol':'str' , 'ValueCol':'str' },
                                    ifExists='fail')
        self.assertTrue(res==True)

        # check already exists with ifExists == 'fail'
        res = dbh.CreateTable('my_test_table123',
                                    colsNamesAndTypes={'keyCol':'str' , 'ValueCol':'str' },
                                    ifExists='fail')
        self.assertTrue(res==False)

        # check already exists with ifExists == 'replace'
        res = dbh.CreateTable('my_test_table123',
                                          colsNamesAndTypes={'keyCol': 'str', 'ValueCol': 'str'},
                                          ifExists='replace')
        self.assertTrue(res == True)

        dbh.deleteTable('my_test_table123')
        self.assertTrue(True)

    def test_table_add_item_and(self):
        dbh = DatabaseHandler(connections_to_check)

        res = dbh.CreateTable('my_test_table123',
                                          colsNamesAndTypes={'keyCol': 'str', 'ValueCol': 'str'},
                                          ifExists='replace')
        self.assertTrue(res == True)
        dbh.addItem('my_test_table123',['keyCol', 'ValueCol'],['abc','def'])
        val1 = dbh.getItem('my_test_table123','keyCol','abc')
        self.assertEqual(val1[0][1], 'def') # One value, second column
        val2 = dbh.getItem('my_test_table123','keyCol','abc',colsNameToGet=['ValueCol'])
        self.assertEqual(val2[0][0], 'def')

        dbh.deleteTable('my_test_table123')

    def test_table_update_item(self):
        dbh = DatabaseHandler(connections_to_check)

        res = dbh.CreateTable('my_test_table123',
                                          colsNamesAndTypes={'keyCol': 'str', 'ValueCol': 'str'},
                                          ifExists='replace')
        self.assertTrue(res == True)
        dbh.addItem('my_test_table123',['keyCol', 'ValueCol'],['abc','def'])
        dbh.updateItem('my_test_table123','keyCol','abc',['hhh'],['ValueCol'])
        val = dbh.getItem('my_test_table123','keyCol','abc',colsNameToGet=['ValueCol'])
        self.assertEqual(val[0][0], 'hhh')

        dbh.deleteTable('my_test_table123')

    def test_load_all(self):
        dbh = DatabaseHandler(connections_to_check)

        res = dbh.CreateTable('my_test_table123',
                                          colsNamesAndTypes={'keyCol': 'str', 'ValueCol': 'str'},
                                          ifExists='replace')
        self.assertTrue(res == True)
        dbh.addItem('my_test_table123',['keyCol', 'ValueCol'],['abc','def'])
        dbh.addItem('my_test_table123',['keyCol', 'ValueCol'],['qwe','rty'])


        val = dbh.loadAllRows('my_test_table123',['ValueCol'])
        self.assertEqual(val[0][0], 'def')
        self.assertEqual(val[1][0],'rty')

        dbh.deleteTable('my_test_table123')

    def test_table_create_with_defaults(self):
        dbh = DatabaseHandler(connections_to_check)

        res = dbh.CreateTable('my_test_table123',
                                          colsNamesAndTypes={'keyCol': 'str', 'ValueCol': 'int'},
                                          colDefValues={'ValueCol': -10},
                                          ifExists='replace')
        self.assertTrue(res)
        dbh.addItem('my_test_table123',['keyCol','ValueCol'],['k1',1])
        dbh.addItem('my_test_table123',['keyCol'],['k2d'])
        dbh.addItem('my_test_table123',['keyCol','ValueCol'],['k3',3])
        val = dbh.getItem('my_test_table123','keyCol','k1',colsNameToGet=['ValueCol'])
        self.assertEqual(val[0][0], 1)
        val = dbh.getItem('my_test_table123','keyCol','k2d',colsNameToGet=['ValueCol'])
        self.assertEqual(val[0][0], -10)
        val = dbh.getItem('my_test_table123','keyCol','k3',colsNameToGet=['ValueCol'])
        self.assertEqual(val[0][0], 3)

        dbh.deleteTable('my_test_table123')

    def test_tablr_add_and_get_columns_names(self):
        dbh = DatabaseHandler(connections_to_check)
        table_name = 'my_test_table123'
        res = dbh.CreateTable(table_name,
                                          colsNamesAndTypes={'keyCol': 'str', 'ValueCol': 'int'},
                                          colDefValues={'ValueCol': -10},
                                          ifExists='replace')

        res = dbh.get_columns_names(table_name)
        assert(res[0]=='keyCol')
        assert(res[1]=='ValueCol')

        dbh.add_column(table_name, {'col1': 'str', 'col2': 'float'}, {'col1': 'tmp', 'col2': '0.1'})
        dbh.add_column(table_name, {'col3' : 'int'})
        res = dbh.get_columns_names(table_name)
        assert(res.__contains__('col1'))
        assert(res.__contains__('col2'))
        assert(res.__contains__('col3'))

        dbh.deleteTable('my_test_table123')

    def test_table_add_columns(self):
        dbh = DatabaseHandler(connections_to_check)

    # def test_table_primary_key(self):
    # dbh = DatabaseHandler(connections_to_check)
    #     res = dbh.CreateTable('my_test_table123',
    #                                       colsNamesAndTypes={'keyCol': 'str', 'ValueCol': 'int'},
    #                                       colDefValues={'ValueCol': -10},
    #                                       ifExists='replace')
    #     self.assertTrue(res)
    #     dbh.addItem('my_test_table123',['keyCol','ValueCol'],['k1',1])
    #     dbh.addItem('my_test_table123',['keyCol'],['k2d'])
    #     dbh.addItem('my_test_table123',['keyCol','ValueCol'],['k3',3])
    #     val = dbh.getItem('my_test_table123','keyCol','k1',colsNameToGet=['ValueCol'])
    #     self.assertEqual(val[0][0], 1)
    #     val = dbh.getItem('my_test_table123','keyCol','k2d',colsNameToGet=['ValueCol'])
    #     self.assertEqual(val[0][0], -10)
    #     val = dbh.getItem('my_test_table123','keyCol','k3',colsNameToGet=['ValueCol'])
    #     self.assertEqual(val[0][0], 3)
    #
    #     dbh.deleteTable('my_test_table123')

if __name__ == '__main__':
    unittest.main()

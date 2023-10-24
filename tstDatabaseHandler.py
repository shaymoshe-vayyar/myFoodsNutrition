import DatabaseHandler
import unittest


class Testing(unittest.TestCase):
    def test_table_create_and_delete(self):
        # Check simple key-value

        DatabaseHandler.deleteTable('my_test_table123')

        res = DatabaseHandler.CreateTable('my_test_table123',
                                    colsNamesAndTypes={'keyCol':'str' , 'ValueCol':'str' },
                                    ifExists='fail')
        self.assertTrue(res==True)

        # check already exists with ifExists == 'fail'
        res = DatabaseHandler.CreateTable('my_test_table123',
                                    colsNamesAndTypes={'keyCol':'str' , 'ValueCol':'str' },
                                    ifExists='fail')
        self.assertTrue(res==False)

        # check already exists with ifExists == 'replace'
        res = DatabaseHandler.CreateTable('my_test_table123',
                                          colsNamesAndTypes={'keyCol': 'str', 'ValueCol': 'str'},
                                          ifExists='replace')
        self.assertTrue(res == True)

        DatabaseHandler.deleteTable('my_test_table123')
        self.assertTrue(True)

    def test_table_exists(self):
        res = DatabaseHandler.checkIfTableExists('blabla')
        self.assertTrue(res == False)

        res = DatabaseHandler.checkIfTableExists('user')
        self.assertTrue(res == True)

    def test_table_add_item_and(self):
        res = DatabaseHandler.CreateTable('my_test_table123',
                                          colsNamesAndTypes={'keyCol': 'str', 'ValueCol': 'str'},
                                          ifExists='replace')
        self.assertTrue(res == True)
        DatabaseHandler.addItem('my_test_table123',['keyCol', 'ValueCol'],['abc','def'])
        val1 = DatabaseHandler.getItem('my_test_table123','keyCol','abc')
        self.assertEqual(val1[0][1], 'def') # One value, second column
        val2 = DatabaseHandler.getItem('my_test_table123','keyCol','abc',colsNameToGet=['ValueCol'])
        self.assertEqual(val2[0][0], 'def')

        DatabaseHandler.deleteTable('my_test_table123')

    def test_table_update_item(self):
        res = DatabaseHandler.CreateTable('my_test_table123',
                                          colsNamesAndTypes={'keyCol': 'str', 'ValueCol': 'str'},
                                          ifExists='replace')
        self.assertTrue(res == True)
        DatabaseHandler.addItem('my_test_table123',['keyCol', 'ValueCol'],['abc','def'])
        DatabaseHandler.updateItem('my_test_table123','keyCol','abc',['hhh'],['ValueCol'])
        val = DatabaseHandler.getItem('my_test_table123','keyCol','abc',colsNameToGet=['ValueCol'])
        self.assertEqual(val[0][0], 'hhh')

        DatabaseHandler.deleteTable('my_test_table123')

    def test_load_all(self):
        res = DatabaseHandler.CreateTable('my_test_table123',
                                          colsNamesAndTypes={'keyCol': 'str', 'ValueCol': 'str'},
                                          ifExists='replace')
        self.assertTrue(res == True)
        DatabaseHandler.addItem('my_test_table123',['keyCol', 'ValueCol'],['abc','def'])
        DatabaseHandler.addItem('my_test_table123',['keyCol', 'ValueCol'],['qwe','rty'])


        val = DatabaseHandler.loadAllRows('my_test_table123',['ValueCol'])
        self.assertEqual(val[0][0], 'def')
        self.assertEqual(val[1][0],'rty')

        DatabaseHandler.deleteTable('my_test_table123')

    def test_table_create_with_defaults(self):
        res = DatabaseHandler.CreateTable('my_test_table123',
                                          colsNamesAndTypes={'keyCol': 'str', 'ValueCol': 'int'},
                                          colDefValues={'ValueCol': -10},
                                          ifExists='replace')
        self.assertTrue(res)
        DatabaseHandler.addItem('my_test_table123',['keyCol','ValueCol'],['k1',1])
        DatabaseHandler.addItem('my_test_table123',['keyCol'],['k2d'])
        DatabaseHandler.addItem('my_test_table123',['keyCol','ValueCol'],['k3',3])
        val = DatabaseHandler.getItem('my_test_table123','keyCol','k1',colsNameToGet=['ValueCol'])
        self.assertEqual(val[0][0], 1)
        val = DatabaseHandler.getItem('my_test_table123','keyCol','k2d',colsNameToGet=['ValueCol'])
        self.assertEqual(val[0][0], -10)
        val = DatabaseHandler.getItem('my_test_table123','keyCol','k3',colsNameToGet=['ValueCol'])
        self.assertEqual(val[0][0], 3)

        DatabaseHandler.deleteTable('my_test_table123')

    # def test_table_primary_key(self):
    #     res = DatabaseHandler.CreateTable('my_test_table123',
    #                                       colsNamesAndTypes={'keyCol': 'str', 'ValueCol': 'int'},
    #                                       colDefValues={'ValueCol': -10},
    #                                       ifExists='replace')
    #     self.assertTrue(res)
    #     DatabaseHandler.addItem('my_test_table123',['keyCol','ValueCol'],['k1',1])
    #     DatabaseHandler.addItem('my_test_table123',['keyCol'],['k2d'])
    #     DatabaseHandler.addItem('my_test_table123',['keyCol','ValueCol'],['k3',3])
    #     val = DatabaseHandler.getItem('my_test_table123','keyCol','k1',colsNameToGet=['ValueCol'])
    #     self.assertEqual(val[0][0], 1)
    #     val = DatabaseHandler.getItem('my_test_table123','keyCol','k2d',colsNameToGet=['ValueCol'])
    #     self.assertEqual(val[0][0], -10)
    #     val = DatabaseHandler.getItem('my_test_table123','keyCol','k3',colsNameToGet=['ValueCol'])
    #     self.assertEqual(val[0][0], 3)
    #
    #     DatabaseHandler.deleteTable('my_test_table123')

if __name__ == '__main__':
    unittest.main()

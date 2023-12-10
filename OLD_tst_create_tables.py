import unittest
from database_handler import DatabaseHandler
import create_tables
import globalContants as gc


class Testing(unittest.TestCase):
    # dbh = DatabaseHandler(['pc'])
    # def test_create_table_nutrition_attribute(self):
    #     res = create_tables.create_table_nutrition_attribute()
    #     self.assertTrue(res)
    #
    #     ##dbh.deleteTable(gc.__table_nutrition_attribute_name__)
    #
    # def test_create_table_items_data(self):
    #     res = create_tables.create_table_items_data()
    #     self.assertTrue(res)
    #
    #     ##dbh.deleteTable(gc.__table_items_data_name__)
    #
    # def test_create_table_daily_items(self):
    #     res = create_tables.create_table_daily_items()
    #     self.assertTrue(res)
    #
    #     ##dbh.deleteTable(gc.__table_daily_items_name__)

if __name__ == '__main__':
    unittest.main()

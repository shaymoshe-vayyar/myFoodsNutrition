## With the user, gets the items and prepare a DB for links
## Then add them to the DB of items' Nut. Values.
## Can recreate the food source table upon request

### https://stackoverflow.com/questions/32289175/list-of-all-tkinter-events
### https://stackoverflow.com/questions/68528274/how-to-raise-an-event-when-enter-is-pressed-into-an-inputtext-in-pysimplegui
### https://www.blog.pythonlibrary.org/2022/01/25/pysimplegui-an-intro-to-laying-out-elements/
### https://www.tutorialspoint.com/pysimplegui/pysimplegui_progressbar_element.htm
### https://www.urlencoder.io/python/

import PySimpleGUI as psg
import requests_cache
import requests
from bs4 import BeautifulSoup
import urllib.parse
from database_handler import DatabaseHandler

import globalContants as gc
import glob



# Internal
def GetListOfOptionalUrls(itemName, isMyRecipe : bool):
    stringsToRemove = ['FoodsDictionary','- FoodsDictionary','ערכים תזונתיים',', ערכים תזונתיים','.html','ערך תזונתי של','ערך תזונתי',',','-']
    if (isMyRecipe):
        myRecipesFolder = r'C:\Users\ShayMoshe\Downloads\MyRecipes'
        files = glob.glob(f'{myRecipesFolder}\\*{itemName}*.html')
        urls = list()
        for file in files:
            name = file[file.rfind('\\')+1:]
            for strToRemove in stringsToRemove:
                name = name.replace(strToRemove, '')
            name = name.strip()
            href = file
            print(name)
            urls.append([name, href, name])
        return urls

    baseQrUrl = fr'http://www.google.com/search?q=site:foodsdictionary.co.il'+urllib.parse.quote(f' {itemName} ערך תזונתי ')+"&num=20"
    searchCategory = 'Products'
    if itemName.find('מתכון') >= 0:
        searchCategory = 'Recipes'
        searchKeyWords = 'מתכון'
        baseQrUrl = fr'http://www.google.com/search?q=site:foodsdictionary.co.il'+urllib.parse.quote(f' {itemName} ')+"&num=20"

    session = requests_cache.CachedSession('cacheUrlName')
    r = session.get(baseQrUrl)
    if (not r.from_cache):
        print('not from cache!')
        # ch = psg.popup_yes_no(f"{itemName} not from Cache",
        #                       "Please Confirm")
        # if (ch != "Yes"):
        #     raise Exception(f'{itemName} not from cache')
    # r = requests.get(baseQrUrl)

    # # Debug
    # f = open(r"c:/temp/tmp22.html",'w')
    # f.write(r.text)
    # raise Exception('file written')

    import re
    links = re.findall(rf'www\.foodsdictionary\.co\.il\/{searchCategory}[\w\%\/]*', r.text)
    # links = re.findall(fr'www\.foodsdictionary\.co\.il\%2F{searchCategory}[\w\%]*',r.text)
    # flag_is_unquote = False
    # if (len(links)==0):
    #     flag_is_unquote = True
    #     links = re.findall(rf'www\.foodsdictionary\.co\.il\/{searchCategory}[\w\%\/]*', r.text)
    urls = list()
    for link in links:
        href = r'http://'+link

        a_element_text_arr = re.findall(r"<a[^>]*?"+link+".*?\/a>",r.text)
        soup = BeautifulSoup(a_element_text_arr[0], 'html.parser')
        fname_element = soup.find('h3')
        if fname_element is None:
            name = soup.find('img').get('alt')
        else:
            name = soup.find('h3').text
        name = name.removesuffix('FoodsDictionary').strip(' -')
        # if (flag_is_unquote):
        # else:
        #     name = href[href.rfind(r"%2F")+3:]
        #     for ii in range(3):
        #         name = urllib.parse.unquote(name)
        urls.append([name, href, name])
        print(name)
        print(href)

    return urls

# Internal
def updateItemToDb(dbh : DatabaseHandler,itemName,itemUrl,itemDesc, isExtended):
    # Check if item already exists
    retItem = dbh.getItem(gc.__table_items_data_name__,'itemName',itemName)
    if len(retItem) > 0:  # Already exists - check that info is the same
        print(f'{itemName} already exists')
        raise Exception(f"'{itemName}' already exists")

    import foodsdicParsing
    parsedItem = foodsdicParsing.ParseUrl(itemUrl)
    parsedItem['itemName'] = itemName
    parsedItem['additionalNames'] = ''
    parsedItem['categoryType'] = 'general'
    parsedItem['nutritionsVSource'] = itemUrl
    parsedItem['isExtended'] = isExtended
    parsedItem['itemsCombination'] = ''
    parsedItem['itemDescription'] = ''
    parsedItem['itemPhotoLink'] = ''

    dbh.addItem(gc.__table_items_data_name__,list(parsedItem.keys()),list(parsedItem.values()))
    print(parsedItem)
    return True,False

def GuiFoodData(dbh : DatabaseHandler):
    psg.set_options(font=("Arial Bold", 14),text_justification="right")
    lst = psg.Listbox([], expand_x=True, key="listboxW", visible=True)
    # toprow = ['פרטים נוספים', 'קישור', 'שם המוצר']
    toprow = ['קישור', 'שם המוצר']
    rows = [[]]
    tbl1 = psg.Table(values=rows, headings=toprow,
                     auto_size_columns=True,
                     display_row_numbers=False,
                     justification='right',
                     key='-TABLE-',
                     selected_row_colors='red on yellow',
                     enable_events=True,
                     expand_x=True,
                     expand_y=True,
                     enable_click_events=True)
    layout = [[psg.Text('מורחב'),psg.Checkbox('',key="_ExtendedCB_"),psg.Text('המתכונים שלי'),psg.Checkbox('',key="_MyRecCB_"), psg.Text('בחר מוצר')],
              [psg.InputText('',enable_events=True,expand_x=True, key="_COMBOINPUT_")],
              [psg.Button('Submit', visible=False, bind_return_key=True)],
              [tbl1],
              [psg.Text('',key='_StatusBar_')]]
    # [lst],
    window = psg.Window("הוספת מוצרים", layout, size=(1200, 800), resizable=True,element_justification="right",finalize=True)
    window["_COMBOINPUT_"].Widget.configure(justify="right")
    selItem = None

    while True:
        event, values = window.read()

        # print("event:", event, "values:", values)
        if event == psg.WIN_CLOSED:
            break
        if event == "Submit":
            urls = GetListOfOptionalUrls(values['_COMBOINPUT_'],values['_MyRecCB_'])
            listNutForTable = []
            for item in urls:
                name = item[0]
                url = item[1]
                nameDetails = item[2]
                listNutForTable.append([
                    # nameDetails, # Details
                    urllib.parse.unquote(urllib.parse.unquote(url)) , # Link
                    name # Item
                ])
            tbl1.update(listNutForTable)
            if len(urls)==0:
                updateStsText = "לא נמצא"
                itemName = values['_COMBOINPUT_']
                msg = f"{itemName}' {updateStsText}'"
                window['_StatusBar_'].update(msg)
                print(msg)
        if '+CLICKED+' in event:
            selRow = event[2][0]
            if (selRow is not None) and (selRow >= 0):
                selItem = tbl1.get()[selRow]
                if values['_MyRecCB_']:
                    urlItem = selItem[0]
                else:
                    urlItem = selItem[0] #[selItem[0].find('http'):]
                itemDesc = selItem[1]
                itemName = itemDesc
                itemName = psg.popup_get_text('בחר שם למוצר', title="אנא אשר", default_text=f'{itemName}')
                itemName = itemName.replace(',','__')
                isExtended = values['_ExtendedCB_']
                if itemName is not None:
                    updateItemRes = updateItemToDb(dbh,itemName,urlItem,itemDesc,isExtended)
                    if (updateItemRes[1]):
                        updateStsText = "כבר קיים"
                    else:
                        updateStsText = "עודכן"
                        # window['_StatusBar_'].update(f"' עודכן {itemName}'")
                    window['_StatusBar_'].update(f"{itemName}' {updateStsText}'")
                    print(f"'{itemName}' Updated!")

    window.close()


def GuiFoodDataEdit(dbh : DatabaseHandler):
    psg.set_options(font=("Arial Bold", 14),text_justification="right")
    lst = psg.Listbox([], expand_x=True, key="listboxW", visible=True)
    toprow = ['קלוריות', 'שם המוצר', 'מספר']
    rows = [[]]
    tbl1 = psg.Table(values=rows, headings=toprow,
                     auto_size_columns=True,
                     display_row_numbers=False,
                     justification='right',
                     key='-TABLE-',
                     selected_row_colors='red on yellow',
                     enable_events=True,
                     expand_x=True,
                     expand_y=True,
                     enable_click_events=True)
    tbl2 = psg.Table(values=[[]], headings=['field','value'],
                     auto_size_columns=True,
                     display_row_numbers=False,
                     justification='left',
                     key='-TABLEFIELDS-',
                     selected_row_colors='red on yellow',
                     enable_events=True,
                     expand_x=True,
                     expand_y=True,
                     enable_click_events=True)
    layout = [[psg.Combo(['Part of a Word','Word','SQL-Like Search','REGEXP'],
                         enable_events=True,  readonly=False, key='_COMBOSRCHMODE_',default_value='Part of a Word'),
               psg.Text('בחר מוצר')],
              [psg.InputText('',enable_events=True,expand_x=True, key="_COMBOINPUT_")],
              [psg.Button('Submit', visible=False, bind_return_key=True)],
              [tbl1],
              [tbl2],
              [psg.Text('',key='_StatusBar_')]]
    # [lst],
    window = psg.Window("הוספת מוצרים", layout, size=(1200, 800), resizable=True,element_justification="right",finalize=True)
    window["_COMBOINPUT_"].Widget.configure(justify="right")
    selItem = None
    list_fields = []

    while True:
        event, values = window.read()

        # print("event:", event, "values:", values)
        if event == psg.WIN_CLOSED:
            break
        if event == "Submit":
            cur_qr = values['_COMBOINPUT_']
            search_mode = values['_COMBOSRCHMODE_']
            # 'Part of a Word','Word','SQL-Like Search','REGEXP'
            match search_mode:
                case 'Part of a Word':
                    search_mode_inp = 'like'
                    search_str = f"%{cur_qr}%"
                case 'Word':
                    search_mode_inp = 'regexp'
                    search_str = f"\\\\b{cur_qr}\\\\b"
                case 'SQL-Like Search':
                    search_mode_inp = 'like'
                    search_str = cur_qr
                case 'REGEXP':
                    search_mode_inp = 'regexp'
                    search_str = cur_qr

            if (len(cur_qr)>1):
                retItems = dbh.searchItem(gc.__table_items_data_name__,
                                          'itemName',
                                          search_str,
                                          search_mode=search_mode_inp)
                print(len(retItems))
                if len(retItems) == 0:
                    window['_StatusBar_'].update(f"{itemName}' לא נמצא '")
                else:
                    listItemsForTable = []
                    for item in retItems:
                        listItemsForTable.append([
                            item['_energy'], # Energy
                            item['itemName'], # Name
                            item['itemUID'] # ID
                        ])
                    tbl1.update(listItemsForTable)

                    list_fields = []
                    for key, value in retItems[0].items():
                        list_fields.append([key,value])
                    tbl2.update(list_fields)
        elif event=='-TABLEFIELDS-':
            print(list_fields[values['-TABLEFIELDS-'][0]])
    window.close()


def createDBNutUnitsForDisplay(dbh : DatabaseHandler):
    dictNutUnitsForDisplayEng = dict()
    import HandleConversion
    dbh.CreateTable(gc.__tableConversionNutUnitsToDisplayName__, gc.__tableConversionNutUnitsToDisplayColNamesNTypes__,ifExists='replace')
    for nutNameHeb in HandleConversion.__dictNutNameToUnitsForDisplay__:
        nutNameEng = HandleConversion.__dictHebNameToEngName__[nutNameHeb]
        unitsEng = HandleConversion.__dictHebNameToEngName__[HandleConversion.__dictNutNameToUnitsForDisplay__[nutNameHeb]]
        dictNutUnitsForDisplayEng[nutNameEng] = unitsEng
        dbh.addItem(gc.__tableConversionNutUnitsToDisplayName__, list(gc.__tableConversionNutUnitsToDisplayColNamesNTypes__.keys()),
                                [nutNameEng, unitsEng])

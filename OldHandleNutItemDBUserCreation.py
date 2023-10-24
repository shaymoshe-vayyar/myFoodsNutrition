import PySimpleGUI as psg
import requests_cache
import urllib.parse

from bs4 import BeautifulSoup

import foodsdicParsing
from DBClasses import DBItemsNutClass

### https://stackoverflow.com/questions/32289175/list-of-all-tkinter-events
### https://stackoverflow.com/questions/68528274/how-to-raise-an-event-when-enter-is-pressed-into-an-inputtext-in-pysimplegui
### https://www.blog.pythonlibrary.org/2022/01/25/pysimplegui-an-intro-to-laying-out-elements/
### https://www.tutorialspoint.com/pysimplegui/pysimplegui_progressbar_element.htm
### https://www.urlencoder.io/python/

def GetListOfOptionalUrls(itemName):
    #baseQrUrl = fr'https://www.google.com/search?q=site:foodsdictionary.co.il {itemName} ערך תזונתי ';
    baseQrUrl = fr'https://www.google.com/search?q=site:foodsdictionary.co.il'+urllib.parse.quote(f' {itemName} ערך תזונתי ')
    session = requests_cache.CachedSession('cacheUrlName')
    r = session.get(baseQrUrl)
    if (not r.from_cache):
        print('not from cache!')
        ch = psg.popup_yes_no(f"{itemName} not from Cache",
                              "Please Confirm")
        if (ch != "yes"):
            raise Exception(f'{itemName} not from cache')

    textToParse = r.text
    soup = BeautifulSoup(textToParse, 'html.parser')
    aElement = soup.find_all('a')
    urls = list()
    for item in aElement:
        if (item.attrs['href']).__contains__('Products') == False:
            continue
        itemText = item.getText(';')
        if itemText.__contains__('ערכים תזונתיים') == False:
            continue
        arrTxt = itemText.split(';')
        selTxt = ''
        for txt in arrTxt:
            if txt.__contains__('ערכים תזונתיים'):
                selTxt = txt
        href = item.attrs['href']
        qq = href[href.find(r'Products/'):href.find(r'&')]
        qa = (qq[qq.rfind('/') + 1:])
        name = urllib.parse.unquote(qa.replace('%25','%'))
        urls.append([name,href,selTxt])
    return urls

def updateItemToDb(itemName,itemUrl):

    parsedItem = foodsdicParsing.ParseUrl(itemUrl)
    parsedItem[DBItemsNutClass.__itemName__] = itemName
    DBItemsNutClass.AddItemToSqlDb(parsedItem)
    #print(parsedItem)

urls = GetListOfOptionalUrls('חסה')
print(urls)
def GuiFoodData():
    psg.set_options(font=("Arial Bold", 14),text_justification="right")
    lst = psg.Listbox([], expand_x=True, key="listboxW", visible=True)
    toprow = ['קלוריות', 'קישור', 'שם המוצר']
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
    layout = [[psg.Text('בחר מוצר')],
              [psg.InputText('',enable_events=True,expand_x=True, key="_COMBOINPUT_")],
              [psg.Button('Submit', visible=False, bind_return_key=True)],[tbl1]]
    # [lst],
    window = psg.Window("הוספת מוצרים", layout, size=(1200, 800), resizable=True,element_justification="right",finalize=True)
    window["_COMBOINPUT_"].Widget.configure(justify="right")
    selItem = None

    while True:
        event, values = window.read()
        print("event:", event, "values:", values)
        if event == psg.WIN_CLOSED:
            break
        if event == "Submit":
            urls = GetListOfOptionalUrls(values['_COMBOINPUT_'])
            listNutForTable = []
            for item in urls:
                name = item[0]
                url = item[1]
                nameDetails = item[2]
                listNutForTable.append([
                    nameDetails, # Details
                    url, # Link
                    name # Item
                ])
            tbl1.update(listNutForTable)
        if '+CLICKED+' in event:
            selRow = event[2][0]
            selItem = tbl1.get()[selRow]
            urlItem = selItem[1][selItem[1].find('http'):]
            ch = psg.popup_yes_no("You clicked row:{}, selected Item:{}, continue?".format(event[2][0], selItem[2]),"Please Confirm")
            if ch == "Yes":
                updateItemToDb(selItem[2],urlItem)
                print('Updated!')

    window.close()
import tabula
import pandas as pd
import sys
import mysql.connector
import os
from datetime import datetime

# Check if a PDF or Excel file is provided as a command line argument
if len(sys.argv) < 2:
    print("Usage: python script.py <pdf_or_excel_file_path>")
    sys.exit(1)

file_path = sys.argv[1]

if not os.path.isfile(file_path):
    print("File does not exist.")
    sys.exit(1)

# Initialize an empty DataFrame to store all pages' data
all_pages_df = pd.DataFrame()

# Read PDF file and concatenate data from all pages
if file_path.lower().endswith(".pdf"):
    # Get a list of DataFrames for each page
    pdf_data = tabula.read_pdf(file_path, pages="all")

    # Concatenate DataFrames from all pages into a single DataFrame
    all_pages_df = pd.concat(pdf_data, ignore_index=True)
else:
    all_pages_df = pd.read_excel(file_path, skiprows=15)

# Skip the first 3 rows, including the header
all_pages_df = all_pages_df[2:]

# Rename the columns
all_pages_df = all_pages_df.rename(columns={'Unnamed: 0': 'Date', 'Unnamed: 1': 'Details', 'Unnamed: 2': 'Debit', 'Unnamed: 3': 'Credit'})

# Remove rows with all NaN values and fill NaN values with empty strings
all_pages_df = all_pages_df.dropna(how='all')
all_pages_df = all_pages_df.fillna(' ')

# Convert date format "22 Oct 2023" to "22-10-2023"
def convert_date(date_str):
    try:
        date_obj = datetime.strptime(date_str, "%d %b %Y")
        return date_obj.strftime("%d/%m/%Y")
    except ValueError:
        return date_str

all_pages_df['Date'] = all_pages_df['Date'].apply(convert_date)

# Database connection details
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'spendwisedb',
}

# Connect to the database
try:
    connection = mysql.connector.connect(**db_config)
    cursor = connection.cursor()

    delete_sql = f"DELETE FROM data"
    cursor.execute(delete_sql)

    # Insert data into your database table
    for index, row in all_pages_df.iterrows():
        date = str(row['Date'])
        particulars = str(row['Details'])

        try:
            withdrawals = float(row['Debit'])
        except (ValueError, TypeError):
            withdrawals = 0  # default value

        try:
            deposits = float(row['Credit'])
        except (ValueError, TypeError):
            deposits = 0  # default value

        if (withdrawals == 0 and deposits == 0) or (withdrawals != 0 and deposits != 0):
            continue

        # Insert data into the database table
        query = "INSERT INTO data (Date, Particulars, Withdrawals, Deposits) VALUES (%s, %s, %s, %s)"
        cursor.execute(query, (date, particulars, withdrawals, deposits))

    connection.commit()
    print("Data inserted into the database.")

except mysql.connector.Error as err:
    print(f"Error: {err}")
finally:
    if connection.is_connected():
        cursor.close()
        connection.close()
        print("Database connection closed.")

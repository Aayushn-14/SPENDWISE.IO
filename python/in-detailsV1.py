import pdfplumber
import pandas as pd
import mysql.connector
import sys
import os

# Function to extract data from the PDF (you will need to implement this)
def extract_data_from_pdf(pdf_file_path):
    # Implement your PDF data extraction here
    # Extract data as a dictionary, e.g., data = {'Account Name': 'John Doe', 'Account Number': '12345', ...}
    data = {}  # Replace with actual data extraction logic
    return data

if len(sys.argv) < 2:
    print("No PDF file path provided.")
    sys.exit(1)

# Get the uploaded PDF file path from the command-line argument
uploaded_file_path = sys.argv[1]

# Check if the uploaded file exists
if not uploaded_file_path:
    print("Uploaded file does not exist.")
    sys.exit(1)

# Open the PDF file using pdfplumber
with pdfplumber.open(uploaded_file_path) as pdf:
    # Access the first page
    first_page = pdf.pages[0]

    # Extract text from the PDF
    pdf_text = first_page.extract_text()

# Define the keywords to search for
keywords = ['Account Name', 'Account Number', 'Branch', 'IFS code', 'Email']

# Initialize variables for data fields
account_name_value = None
account_number_value = None
branch_value = None
ifs_code_value = None
email_value = 'NA'  # Default value

# Split the extracted text into lines
pdf_lines = pdf_text.split('\n')

# Iterate through the lines to extract the data
for line in pdf_lines:
    for keyword in keywords:
        if keyword in line:
            parts = line.split(keyword)
            if len(parts) > 1:
                value = parts[1].strip()
                if keyword == 'Account Name':
                    account_name_value = value
                elif keyword == 'Account Number':
                    account_number_value = value
                elif keyword == 'Branch':
                    branch_value = value
                elif keyword == 'IFS code':
                    ifs_code_value = value
                elif keyword == 'Email':
                    email_value = value

# # Create a pandas DataFrame with the extracted data
# data = {
#     'Name': [account_name_value],
#     'Bank Name': [branch_value],
#     'IFSC': [ifs_code_value],
#     'Account Number': [account_number_value],
#     'Email': [email_value]
# }

# Convert the data dictionary into a pandas DataFrame
# df = pd.DataFrame(data)

# Connect to your MySQL database (replace with your connection details)
db_config = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "spendwisedb"
}

conn = mysql.connector.connect(**db_config)

# Check if the connection is successful
if conn.is_connected():
    cursor = conn.cursor()
# ... (Previous code remains the same)

# Iterate through DataFrame rows and insert data into MySQL table
# for index, row in df.iterrows():
#     try:
#         cursor.execute("""
#             INSERT INTO clients ('Name', `Bank Name`, 'IFSC', `Account Number`, 'Email')
#             VALUES (%s, %s, %s, %s, %s)
#         """, (row['Name'], row['Bank Name'], row['IFSC'], row['Account Number'], row['Email']))
        
#         # Commit changes for each row insertion (optional, can be adjusted based on needs)
#         conn.commit()
#         print("Row inserted successfully")
#     except mysql.connector.Error as err:
#         print("MySQL Error:", err)
#         conn.rollback()  # Rollback changes if an error occurs
#     except Exception as e:
#         print("An error occurred while inserting row:")
#         print("Exception details:", e)

# # Close cursor and connection
# cursor.close()
# conn.close()
sql = "INSERT INTO clients (`Name`, `Bank Name`, `Account Number`, `IFSC`, `Email`) VALUES (%s, %s, %s, %s, %s)"
values_to_insert = (name_value, bank_name_value, account_number_value, ifs_code_value, email_value)

try:
    cursor.execute(sql, values_to_insert)
    
    # Commit changes to the database
    conn.commit()
    
    print("Data inserted into the database table successfully")
except mysql.connector.Error as err:
    print("MySQL Error:", err)
    conn.rollback()  # Rollback changes if an error occurs
except Exception as e:
    print("An error occurred while inserting data:")
    print("Exception details:", e)

# Close cursor and connection
cursor.close()
conn.close()
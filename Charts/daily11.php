<!DOCTYPE html>
<html>
<head>
    <script src="https://d3js.org/d3.v5.min.js"></script>
</head>
<body>
    <svg id="myChart" width="100%" height="400"></svg>

    <?php
    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "spendwisedb";

    // Create a connection to the database
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // SQL query to retrieve data from the database
    $query = "SELECT Date, Withdrawals, Deposits FROM Data";
    $result = $conn->query($query);

    $dataPoints = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $date = strtotime($row["Date"]); // Convert date to timestamp
            $withdrawals = $row["Withdrawals"];
            $deposits = $row["Deposits"];
            $dataPoints[] = array("date" => $date, "withdrawals" => $withdrawals, "deposits" => $deposits);
        }
    }

    // Close the database connection
    $conn->close();
    ?>

    <script>
        // JavaScript code to create the bar chart using D3.js
        const dataPoints = <?php echo json_encode($dataPoints); ?>;
        
        // Function to create the bar chart
        function createBarChart(data) {
            const svg = d3.select("#myChart");
            const margin = { top: 20, right: 30, bottom: 40, left: 40 };
            const width = svg.attr("width") - margin.left - margin.right;
            const height = svg.attr("height") - margin.top - margin.bottom;
            
            const xScale = d3.scaleBand()
                .domain(data.map(d => new Date(d.date * 1000).toISOString().slice(0, 10))) // Convert timestamp to date string
                .range([margin.left, width])
                .padding(0.1);

            const yScale = d3.scaleLinear()
                .domain([0, d3.max(data, d => Math.max(d.withdrawals, d.deposits))])
                .nice()
                .range([height, margin.top]);
            
            const g = svg.append("g")
                .attr("transform", `translate(${margin.left}, ${margin.top})`);
            
            g.selectAll(".bar")
                .data(data)
                .enter().append("rect")
                .attr("class", "bar")
                .attr("x", d => xScale(new Date(d.date * 1000).toISOString().slice(0, 10))) // Convert timestamp to date string
                .attr("y", d => yScale(Math.max(d.withdrawals, d.deposits)))
                .attr("width", xScale.bandwidth())
                .attr("height", d => height - yScale(Math.max(d.withdrawals, d.deposits)))
                .attr("fill", d => d.withdrawals > d.deposits ? "#b91d47" : "#00aba9");
            
            g.append("g")
                .attr("class", "x axis") // Added the class "x axis"
                .attr("transform", `translate(0, ${height})`)
                .call(d3.axisBottom(xScale))
                .selectAll("text")
                .style("text-anchor", "end")
                .attr("transform", "rotate(-45)");
            
            g.append("g")
                .attr("class", "y axis")
                .call(d3.axisLeft(yScale));
            
            g.append("text")
                .attr("class", "x label")
                .attr("text-anchor", "end")
                .attr("x", width)
                .attr("y", height + margin.top + 20)
                .text("Date");
            
            g.append("text")
                .attr("class", "y label")
                .attr("text-anchor", "end")
                .attr("y", -40)
                .attr("x", -40)
                .attr("dy", ".75em")
                .attr("transform", "rotate(-90)")
                .text("Amount");
        }

        // Call the function to create the chart
        createBarChart(dataPoints);
    </script>
</body>
</html>

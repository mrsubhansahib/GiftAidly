/*
Template Name: Taplox- Responsive Bootstrap 5 Admin Dashboard
Author: Stackbros
File: datatable js
*/
import gridjs  from 'gridjs/dist/gridjs.umd.js';

class GridDatatable {

     init() {
          this.GridjsTableInit();
     }

     GridjsTableInit() {

          // Basic Table
          const el = document.getElementById("table-gridjs");
          if (!el) return;
          // parse safely
          let rows, cols;
          try { rows = JSON.parse(el.dataset.rows || "[]"); } catch { rows = []; }
          try { cols = JSON.parse(el.dataset.columns || "[]"); } catch { cols = []; }
          const limit = Number.parseInt(el.dataset.limit || "10", 10) || 10;
          // rows must be an array
          if (!Array.isArray(rows)) rows = [];
          // infer columns & normalize data
          let columns = [];
          let data = rows;
          if (Array.isArray(cols) && cols.length) {
          columns = cols;
          if (rows.length && !Array.isArray(rows[0]) && typeof rows[0] === "object") {
          data = rows.map(r => cols.map(k => r?.[k]));
          }
          } else if (rows.length) {
          if (Array.isArray(rows[0])) {
          const maxLen = rows.reduce((m, r) => Math.max(m, Array.isArray(r) ? r.length : 0), 0);
          columns = Array.from({ length: maxLen }, (_, i) => `Col ${i + 1}`);
          } else if (typeof rows[0] === "object") {
          const keys = Object.keys(rows[0]);
          columns = keys;
          data = rows.map(r => keys.map(k => r?.[k]));
          } else {
          columns = [];
          data = [];
          }
          }
          // clear host
          el.innerHTML = "";
          // configure Grid.js columns with safe formatter
          const gridColumns = (columns || []).map((c) => {
          const name = typeof c === "string" ? c : (c?.name ?? "");
          // Special handling for "Detail" column
          if (name === "Detail") {
          return {
               name,
               formatter: (cell, row) => {
               // ✅ If backend sends object → { text, url, color }
               if (cell && typeof cell === "object") {
                    const text = cell.text || "Detail";
                    const url = cell.url || "#";
                    const color = cell.color || "primary";
                    return gridjs.html(
                    `<a href="${url}" class="btn btn-sm btn-${color}">${text}</a>`
                    );
               }
               // ✅ Fallback → use row ID for default URL
               const id = row?.cells?.[0]?.data ?? "";
               return gridjs.html(
                    `<a href="/detail/${id}" class="btn btn-sm btn-primary">Detail</a>`
               );
               },
          };
          }
          // Default → show normal cell, render HTML if string starts with "<"
          return {
          name,
          formatter: (cell) => {
               if (typeof cell === "string" && cell.trim().startsWith("<")) {
               return gridjs.html(cell);
               }
               return cell ?? "";
          },
          };
          });
          // render Grid
          new gridjs.Grid({
          columns: gridColumns,
          data,
          search: true,
          sort: true,
          pagination: { limit },
          }).render(el);

          // pagination Table
          if (document.getElementById("table-pagination"))
               new gridjs.Grid({
                    columns: [{
                         name: 'ID',
                         width: '120px',
                         formatter: (function (cell) {
                              return gridjs.html('<a href="" class="fw-medium">' + cell + '</a>');
                         })
                    }, "Name", "Date", "Total",
                    {
                         name: 'Actions',
                         width: '100px',
                         formatter: (function (cell) {
                              return gridjs.html("<button type='button' class='btn btn-sm btn-light'>" +
                                   "Details" +
                                   "</button>");
                         })
                    },
                    ],
                    pagination: {
                         limit: 5
                    },

                    data: [
                         ["#RB2320", "Alice", "07 Oct, 2024", "$24.05"],
                         ["#RB8652", "Bob", "07 Oct, 2024", "$26.15"],
                         ["#RB8520", "Charlie", "06 Oct, 2024", "$21.25"],
                         ["#RB9512", "David", "05 Oct, 2024", "$25.03"],
                         ["#RB7532", "Eve", "05 Oct, 2024", "$22.61"],
                         ["#RB9632", "Frank", "04 Oct, 2024", "$24.05"],
                         ["#RB7456", "Grace", "04 Oct, 2024", "$26.15"],
                         ["#RB3002", "Hannah", "04 Oct, 2024", "$21.25"],
                         ["#RB9857", "Ian", "03 Oct, 2024", "$22.61"],
                         ["#RB2589", "Jane", "03 Oct, 2024", "$25.03"],
                    ]
               }).render(document.getElementById("table-pagination"));

          // search Table
          if (document.getElementById("table-search"))
               new gridjs.Grid({
                    columns: ["Name", "Email", "Position", "Company", "Country"],
                    pagination: {
                         limit: 5
                    },
                    search: true,
                    data: [
                         ["Alice", "alice@example.com", "Software Engineer", "ABC Company", "United States"],
                         ["Bob", "bob@example.com", "Product Manager", "XYZ Inc", "Canada"],
                         ["Charlie", "charlie@example.com", "Data Analyst", "123 Corp", "Australia"],
                         ["David", "david@example.com", "UI/UX Designer", "456 Ltd", "United Kingdom"],
                         ["Eve", "eve@example.com", "Marketing Specialist", "789 Enterprises", "France"],
                         ["Frank", "frank@example.com", "HR Manager", "ABC Company", "Germany"],
                         ["Grace", "grace@example.com", "Financial Analyst", "XYZ Inc", "Japan"],
                         ["Hannah", "hannah@example.com", "Sales Representative", "123 Corp", "Brazil"],
                         ["Ian", "ian@example.com", "Software Developer", "456 Ltd", "India"],
                         ["Jane", "jane@example.com", "Operations Manager", "789 Enterprises", "China"]
                    ]
               }).render(document.getElementById("table-search"));

          // Sorting Table
          if (document.getElementById("table-sorting"))
               new gridjs.Grid({
                    columns: ["Name", "Email", "Position", "Company", "Country"],
                    pagination: {
                         limit: 5
                    },
                    sort: true,
                    data: [
                         ["Alice", "alice@example.com", "Software Engineer", "ABC Company", "United States"],
                         ["Bob", "bob@example.com", "Product Manager", "XYZ Inc", "Canada"],
                         ["Charlie", "charlie@example.com", "Data Analyst", "123 Corp", "Australia"],
                         ["David", "david@example.com", "UI/UX Designer", "456 Ltd", "United Kingdom"],
                         ["Eve", "eve@example.com", "Marketing Specialist", "789 Enterprises", "France"],
                         ["Frank", "frank@example.com", "HR Manager", "ABC Company", "Germany"],
                         ["Grace", "grace@example.com", "Financial Analyst", "XYZ Inc", "Japan"],
                         ["Hannah", "hannah@example.com", "Sales Representative", "123 Corp", "Brazil"],
                         ["Ian", "ian@example.com", "Software Developer", "456 Ltd", "India"],
                         ["Jane", "jane@example.com", "Operations Manager", "789 Enterprises", "China"]
                    ]
               }).render(document.getElementById("table-sorting"));


          // Loading State Table
          if (document.getElementById("table-loading-state"))
               new gridjs.Grid({
                    columns: ["Name", "Email", "Position", "Company", "Country"],
                    pagination: {
                         limit: 5
                    },
                    sort: true,
                    data: function () {
                         return new Promise(function (resolve) {
                              setTimeout(function () {
                                   resolve([
                                        ["Alice", "alice@example.com", "Software Engineer", "ABC Company", "United States"],
                                        ["Bob", "bob@example.com", "Product Manager", "XYZ Inc", "Canada"],
                                        ["Charlie", "charlie@example.com", "Data Analyst", "123 Corp", "Australia"],
                                        ["David", "david@example.com", "UI/UX Designer", "456 Ltd", "United Kingdom"],
                                        ["Eve", "eve@example.com", "Marketing Specialist", "789 Enterprises", "France"],
                                        ["Frank", "frank@example.com", "HR Manager", "ABC Company", "Germany"],
                                        ["Grace", "grace@example.com", "Financial Analyst", "XYZ Inc", "Japan"],
                                        ["Hannah", "hannah@example.com", "Sales Representative", "123 Corp", "Brazil"],
                                        ["Ian", "ian@example.com", "Software Developer", "456 Ltd", "India"],
                                        ["Jane", "jane@example.com", "Operations Manager", "789 Enterprises", "China"]
                                   ])
                              }, 2000);
                         });
                    }
               }).render(document.getElementById("table-loading-state"));


          // Fixed Header
          if (document.getElementById("table-fixed-header"))
               new gridjs.Grid({
                    columns: ["Name", "Email", "Position", "Company", "Country"],
                    sort: true,
                    pagination: true,
                    fixedHeader: true,
                    height: '400px',
                    data: [
                         ["Alice", "alice@example.com", "Software Engineer", "ABC Company", "United States"],
                         ["Bob", "bob@example.com", "Product Manager", "XYZ Inc", "Canada"],
                         ["Charlie", "charlie@example.com", "Data Analyst", "123 Corp", "Australia"],
                         ["David", "david@example.com", "UI/UX Designer", "456 Ltd", "United Kingdom"],
                         ["Eve", "eve@example.com", "Marketing Specialist", "789 Enterprises", "France"],
                         ["Frank", "frank@example.com", "HR Manager", "ABC Company", "Germany"],
                         ["Grace", "grace@example.com", "Financial Analyst", "XYZ Inc", "Japan"],
                         ["Hannah", "hannah@example.com", "Sales Representative", "123 Corp", "Brazil"],
                         ["Ian", "ian@example.com", "Software Developer", "456 Ltd", "India"],
                         ["Jane", "jane@example.com", "Operations Manager", "789 Enterprises", "China"]
                    ]
               }).render(document.getElementById("table-fixed-header"));


          // Hidden Columns
          if (document.getElementById("table-hidden-column"))
               new gridjs.Grid({
                    columns: ["Name", "Email", "Position", "Company",
                         {
                              name: 'Country',
                              hidden: true
                         },
                    ],
                    pagination: {
                         limit: 5
                    },
                    sort: true,
                    data: [
                         ["Alice", "alice@example.com", "Software Engineer", "ABC Company", "United States"],
                         ["Bob", "bob@example.com", "Product Manager", "XYZ Inc", "Canada"],
                         ["Charlie", "charlie@example.com", "Data Analyst", "123 Corp", "Australia"],
                         ["David", "david@example.com", "UI/UX Designer", "456 Ltd", "United Kingdom"],
                         ["Eve", "eve@example.com", "Marketing Specialist", "789 Enterprises", "France"],
                         ["Frank", "frank@example.com", "HR Manager", "ABC Company", "Germany"],
                         ["Grace", "grace@example.com", "Financial Analyst", "XYZ Inc", "Japan"],
                         ["Hannah", "hannah@example.com", "Sales Representative", "123 Corp", "Brazil"],
                         ["Ian", "ian@example.com", "Software Developer", "456 Ltd", "India"],
                         ["Jane", "jane@example.com", "Operations Manager", "789 Enterprises", "China"]
                    ]
               }).render(document.getElementById("table-hidden-column"));
     }

}

document.addEventListener('DOMContentLoaded', function (e) {
     new GridDatatable().init();
});

document.addEventListener("livewire:load", () => {
  Livewire.hook("message.processed", () => {
    new GridDatatable().init();
  });
});
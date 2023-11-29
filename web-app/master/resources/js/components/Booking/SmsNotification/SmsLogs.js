import React, { useEffect, useState } from 'react'
import { Table } from 'antd'

function SmsLogs(props) {
    const { data, loading } = props;

    const columns = [
        {
            title: "Recipient",
            render: (text, record) => {
                return record.recipient;
            }
        },
        {
            title: "Message",
            width: 200,
            ellipsis: true,
            render: (text, record) => {
                return record.message;
            }
        },
        {
            title: "Sender Name",
            render: (text, record) => {
                return record.sender_name;
            }
        },
        {
            title: "Network",
            render: (text, record) => {
                return record.network;
            }
        },
        {
            title: "Sending Type",
            render: (text, record) => {
                return record.sending_type;
            }
        },
        {
            title: "Created By",
            render: (text, record) => {
                return `${record.first_name} ${record.last_name}`;
            }
        },
        {
            title: "Created At",
            render: (text, record) => {
                return record.created_date;
            }
        },
    ];

    return (
        <>
            <Table
               size="small"
               rowKey="id"
               dataSource={data}
               loading={loading}
               columns={columns}
           />
        </>
     );
}

export default SmsLogs;
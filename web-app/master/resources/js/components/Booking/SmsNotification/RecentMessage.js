import React, { useEffect, useState } from 'react'
import { Table } from 'antd'

function RecentMessage(props) {
    const { data, loading } = props;

    const columns = [
        {
            title: "Message #",
            render: (text, record) => {
                return record.message_id;
            }
        },
        {
            title: "Recipient",
            render: (text, record) => {
                return record.recipient;
            }
        },
        {
            title: "Sender Name",
            render: (text, record) => {
                return record.sender_name;
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
            title: "Status",
            render: (text, record) => {
                return record.status;
            }
        },
        {
            title: "Created At",
            render: (text, record) => {
                return record.created_at;
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

export default RecentMessage;
import Http from 'utils/Http'
import { useQuery, useMutation } from 'react-query'

const sendBulkMessage = () => {

    return useMutation(async formData => {
        return await Http.post('/api/sms-notification/send-bulk-message', formData)
    });

}

const getMessages = () => {

    return useQuery("sms-messages", async () => {
        const { data } = await Http.get('/api/sms-notification/get-messages');
        return data;
    });

}

const sendImportBulkMessage = () => {

    return useMutation(async formData => {
        return await Http.post('/api/sms-notification/send-import-bulk-message', formData)
    });

}

const downloadRecepientTemplate = (formData) => {

    Http.post('/api/sms-notification/download-recepient-template',formData,{responseType: 'blob'}).then(({data}) => {

            const downloadUrl = window.URL.createObjectURL(new Blob([data]));
            const link = document.createElement('a');

            const now = new Date().toLocaleDateString();

            link.href = downloadUrl;
            link.setAttribute('download', `upload-recipient-template-${now}.xlsx`); 
            document.body.appendChild(link);
            link.click();
            link.remove();

    });

}

const getAccountDetails = () => {

    return useQuery("sms-accounts", async () => {
            const { data } = await Http.get('/api/sms-notification/get-accounts');
            return data;
    },{
        refetchOnWindowFocus: false
    });

}

const getSmsLogs = (subject = null) => {

    return useQuery(["sms-logs", subject], async () => {
        const { data } = await Http.get(`/api/sms-notification/get-sms-logs/${subject}`);
        return data;
    });

}

const getSmsSubjects = () => {

    return useQuery("sms-subjects", async () => {
        const { data } = await Http.get('/api/sms-notification/get-sms-subjects');
        return data;
    });
}

const createSmsSubject = () => {

    return useMutation(async formData => {
        return await Http.post('/api/sms-notification/create-sms-subject', formData)
    });

}

const createSmsEvent = () => {

    return useMutation(async formData => {
        return await Http.post('/api/sms-notification/create-sms-event', formData)
    });

}

const getSmsEvent = () => {

    return useQuery("sms-event", async () => {
        const { data } = await Http.get('/api/sms-notification/get-sms-event');
        return data;
    });
}

const updateSmsEvent = () => {

    return useMutation(async formData => {
        return await Http.post('/api/sms-notification/update-sms-event', formData)
    });

}

const removeSmsEvent = () => {

    return useMutation(async formData => {
        return await Http.post('/api/sms-notification/remove-sms-event', formData)
    });

}

const getSmsEventJobHistory = () => {

    return useQuery("sms-event-job-history", async () => {
        const { data } = await Http.get('/api/sms-notification/get-sms-job-history');
        return data;
    });
}

const removeJobs = () => {

    return useMutation(async formData => {
        return await Http.post('/api/sms-notification/remove-event-job', formData)
    });

}

const downloadEventData = (formData) => {

    Http.post('/api/sms-notification/download-event-job',formData,{responseType: 'blob'}).then(({data}) => {

            const downloadUrl = window.URL.createObjectURL(new Blob([data]));
            const link = document.createElement('a');

            const now = new Date().toLocaleDateString();

            link.href = downloadUrl;
            link.setAttribute('download', `download-event-job-${now}.xlsx`); 
            document.body.appendChild(link);
            link.click();
            link.remove();

    });

}

export default {
    sendBulkMessage,
    getMessages,
    sendImportBulkMessage,
    downloadRecepientTemplate,
    getAccountDetails,
    getSmsLogs,
    getSmsSubjects,
    createSmsSubject,
    createSmsEvent,
    getSmsEvent,
    updateSmsEvent,
    removeSmsEvent,
    getSmsEventJobHistory,
    removeJobs,
    downloadEventData
}
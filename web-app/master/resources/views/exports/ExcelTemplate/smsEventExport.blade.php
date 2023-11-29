<table> 
    <tbody>     
        
            <tr>
                <th style="font-weight: bold;">Event Name: </th>
                <th style="font-weight: bold;">{{ $data->event_name }}</th>
            </tr>     
            <tr>
                <th style="font-weight: bold;">Event Description: </th>
                <th style="font-weight: bold;">{{ $data->event_description }}</th>
            </tr>
            <tr>
                <th style="font-weight: bold;">Message: </th>
                <th style="font-weight: bold;">{{ $data->message }}</th>
            </tr>
            <tr>
                <th style="font-weight: bold;">Status: </th>
                <th style="font-weight: bold;">{{ $data->status }}</th>
            </tr> 
                                                             
            <tr>
                <th align="center" style="font-weight: bold;">#</th>
                <th align="center" style="font-weight: bold;">Recipient #</th>
                <th align="center" style="font-weight: bold;">Recipient name</th>
            </tr> 
            
            @foreach(json_decode($data->recepients) as $key => $recepient)
            <tr>
                <td align="center" >{{ $key+1 }}</td>
                <td align="center">{{ $recepient->recepient }}</td>
                <td align="center" >{{ $recepient->recepient_name }}</td>
            </tr> 
            @endforeach
           
    </tbody>
</table>

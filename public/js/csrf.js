
// Add CSRF token to all POST requests
$.ajaxPrefilter(function(options, originalOptions, jqXHR)
{
	var type = options.type.toLowerCase();
	if(type == 'post' || type == 'put' || type == 'patch')
	{
		if(!options.crossDomain)
		{
			if(token)
			{
				return jqXHR.setRequestHeader('X-CSRF-Token', token);
			}
		}
	}
});
FB Privacy Auth

This library lets you implement an authorization system in your application
using the same style that Facebook uses for content. Each resource has a
defined list of users and groups that are allowed access to the resource, and
a defined list of users and groups that are not allowed access to the resource.

To use the library, construct an instance of \MattyG\FBPrivacyAuth\AuthChecker,
passing it two arrays. The first is an array where the keys are the identifiers
of each group, and the values are arrays of usernames that are part of each
group. The second is an array where the keys are the identifiers of each
resource, and the values are an array that describes who is allowed access to
each resource. You can see an example of the expected structure in the
"resources.json" file in the tests folder.

When you want to test whether or not a particular user has access to a
particular resource, call the check() function, passing it the identifier of
the given resource, and the username in question. It will then perform the
following checks, in this order:

1. Is this user specifically denied from accessing this resource? If so, DENY.
2. Is this user specifically allowed to access this resource? If so, ALLOW.
3. Is this user a member of a group that is specifically denied from accessing
   this resource? If so, DENY.
4. Is this user a member of a group that is specifically allowed to access
   this resource? If so, ALLOW.
If the user does not match any of the above rules, DENY.

As you can see, the above logic means your application will default towards
being more secure rather than less secure. It is better for a user to be
accidentally locked out of a system than it is to accidentally allow an
unwanted visitor access to the system.

The AuthChecker class also provides a convenient method for retrieving the
identifiers of all resources that a particular user has access to. It does this
by iterating over all resources and checking them one by one with the above
logic. To utilise this, call the getAllowedResourceIds() method and pass it
a username.

Why should you use this, instead of much more established and well-known
solutions such as the Zend Acl or Symfony Security packages? Simplicity. Before
I built this, I evaluated these and a few other potential solutions. I decided
to build my own because of all the things I didn't want out of an authorization
system:

- My resources are not hierarchial, nor are they tied to HTTP routes
- My authentication is already handled elsewhere
- My users have no behaviours or properties associated with them and therefore
  are not objects
- My configuration is in JSON, not YAML
- My priority is speed, not flexibility

I was unable to find an existing solution that fit all of these criteria before
building this.


This software is released into the public domain without any warranty.
